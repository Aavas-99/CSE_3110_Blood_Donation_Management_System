<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Donor;
use App\Models\Patient;
use App\Models\EmergencyRequest;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HospitalController extends Controller
{
    // ── Register ──────────────────────────────────────────
    public function showRegister()
    {
        return view('hospital.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:hospitals,email',
            'phone'    => 'required|string|max:20',
            'district' => 'required|string|max:100',
            'address'  => 'required|string',
            'website'  => 'nullable|url|max:200',
            'password' => [
                'required', 'string', 'min:8', 'max:64', 'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&^_\-]/',
            ],
        ], [
            'email.unique'       => 'This email is already registered.',
            'password.confirmed' => 'Passwords do not match.',
            'password.regex'     => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        Hospital::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'phone'    => $request->phone,
            'district' => $request->district,
            'address'  => $request->address,
            'website'  => $request->website,
        ]);

        return redirect()->route('hospital.login')
                         ->with('success', 'Hospital registered successfully! Please sign in.');
    }

    // ── Login ─────────────────────────────────────────────
    public function showLogin()
    {
        if (session()->has('hospital_id')) {
            return redirect()->route('hospital.dashboard');
        }
        return view('hospital.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email address is required.',
            'password.required' => 'Password is required.',
        ]);

        $hospital = Hospital::where('email', $request->email)->first();

        if (!$hospital || !Hash::check($request->password, $hospital->password)) {
            return back()
                ->withInput(['email' => $request->email])
                ->with('error', 'Invalid email or password. Please try again.');
        }

        session([
            'hospital_id'   => $hospital->hospital_id,
            'hospital_name' => $hospital->name,
            'hospital_email'=> $hospital->email,
            'role'          => 'hospital',
        ]);

        return redirect()->route('hospital.dashboard')
                         ->with('success', 'Welcome back, ' . $hospital->name . '!');
    }

    // ── Dashboard ─────────────────────────────────────────
    public function dashboard(Request $request)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login')
                             ->with('error', 'Please login to access the dashboard.');
        }

        $hospitalId = session('hospital_id');

        // ── Overview Stats ────────────────────────────────
        $totalRequests = EmergencyRequest::count();
        $totalDonations = Donation::where('hospital_id', $hospitalId)->count();
        $totalPatientsServed = Donation::where('hospital_id', $hospitalId)
            ->whereNotNull('patient_id')
            ->distinct('patient_id')
            ->count('patient_id');

        $requestStatusSummary = EmergencyRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Blood Requests ────────────────────────────────
        $reqQuery = EmergencyRequest::with('patient');

        if ($request->filled('req_search')) {
            $search = $request->req_search;
            $reqQuery->where(function($q) use ($search) {
                $q->where('req_id', 'like', "%$search%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('req_status') && $request->req_status !== 'all') {
            $reqQuery->where('status', $request->req_status);
        }

        if ($request->filled('req_urgency') && $request->req_urgency !== 'all') {
            $reqQuery->where('urgency_level', $request->req_urgency);
        }

        $reqSort = $request->req_sort ?? 'newest';
        switch ($reqSort) {
            case 'urgency':
                $reqQuery->orderByRaw("FIELD(urgency_level, 'critical', 'urgent', 'normal')");
                break;
            case 'units_desc':
                $reqQuery->orderBy('quantity_units', 'desc');
                break;
            case 'units_asc':
                $reqQuery->orderBy('quantity_units', 'asc');
                break;
            default:
                $reqQuery->latest('created_at');
        }

        $requests = $reqQuery->paginate(10, ['*'], 'req_page')->withQueryString();

        // ── Donors ────────────────────────────────────────
        $donorQuery = Donor::query();

        if ($request->filled('donor_search')) {
            $search = $request->donor_search;
            $donorQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('blood_group', 'like', "%$search%");
            });
        }

        if ($request->filled('donor_blood') && $request->donor_blood !== 'all') {
            $donorQuery->where('blood_group', $request->donor_blood);
        }

        if ($request->filled('donor_district') && $request->donor_district !== 'all') {
            $donorQuery->where('district', $request->donor_district);
        }

        if ($request->filled('donor_status') && $request->donor_status !== 'all') {
            $donorQuery->where('status', $request->donor_status);
        }

        $donorSort = $request->donor_sort ?? 'name_asc';
        switch ($donorSort) {
            case 'last_donation':
                $donorQuery->orderBy('last_donated_at', 'desc');
                break;
            case 'name_desc':
                $donorQuery->orderBy('name', 'desc');
                break;
            default:
                $donorQuery->orderBy('name', 'asc');
        }

        $donors = $donorQuery->paginate(10, ['*'], 'donor_page')->withQueryString();

        // ── Patients ──────────────────────────────────────
        $patientQuery = Patient::withCount('emergencyRequests');

        if ($request->filled('patient_search')) {
            $search = $request->patient_search;
            $patientQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('patient_blood') && $request->patient_blood !== 'all') {
            $patientQuery->where('blood_group', $request->patient_blood);
        }

        if ($request->filled('patient_district') && $request->patient_district !== 'all') {
            $patientQuery->where('district', $request->patient_district);
        }

        $patients = $patientQuery->paginate(10, ['*'], 'patient_page')->withQueryString();

        // ── Donations ─────────────────────────────────────
        $donationQuery = Donation::with(['donor', 'patient', 'request'])
            ->where('hospital_id', $hospitalId);

        if ($request->filled('donation_search')) {
            $search = $request->donation_search;
            $donationQuery->where(function($q) use ($search) {
                $q->whereHas('donor', function($dq) use ($search) {
                    $dq->where('name', 'like', "%$search%");
                })->orWhereHas('patient', function($pq) use ($search) {
                    $pq->where('name', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('donation_status') && $request->donation_status !== 'all') {
            $donationQuery->where('status', $request->donation_status);
        }

        $donationSort = $request->donation_sort ?? 'newest';
        switch ($donationSort) {
            case 'oldest':
                $donationQuery->oldest('date');
                break;
            case 'quantity_desc':
                $donationQuery->orderBy('quantity_units', 'desc');
                break;
            case 'quantity_asc':
                $donationQuery->orderBy('quantity_units', 'asc');
                break;
            default:
                $donationQuery->latest('date');
        }

        $donations = $donationQuery->paginate(10, ['*'], 'donation_page')->withQueryString();

        // ── Dropdown data ─────────────────────────────────
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $districts = ['Bagerhat','Bandarban','Barguna','Barishal','Bhola','Bogura',
            'Brahmanbaria','Chandpur','Chapai Nawabganj','Chattogram','Chuadanga','Cox\'s Bazar',
            'Cumilla','Dhaka','Dinajpur','Faridpur','Feni','Gaibandha','Gazipur','Gopalganj',
            'Habiganj','Jamalpur','Jessore','Jhalokathi','Jhenaidah','Joypurhat','Khagrachari',
            'Khulna','Kishoreganj','Kurigram','Kushtia','Lakshmipur','Lalmonirhat','Madaripur',
            'Magura','Manikganj','Meherpur','Moulvibazar','Munshiganj','Mymensingh','Naogaon',
            'Narail','Narayanganj','Narsingdi','Natore','Netrokona','Nilphamari','Noakhali',
            'Pabna','Panchagarh','Patuakhali','Pirojpur','Rajbari','Rajshahi','Rangamati',
            'Rangpur','Satkhira','Shariatpur','Sherpur','Sirajganj','Sunamganj','Sylhet',
            'Tangail','Thakurgaon'];

        return view('hospital.dashboard', compact(
            'totalRequests', 'totalDonations', 'totalPatientsServed', 'requestStatusSummary',
            'requests', 'donors', 'patients', 'donations',
            'bloodGroups', 'districts'
        ));
    }

    // ── Request Actions ───────────────────────────────────
    public function approveRequest($id)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login');
        }

        $req = EmergencyRequest::findOrFail($id);
        $req->update(['status' => 'approved']);

        return back()->with('success', 'Request #' . $id . ' approved successfully.');
    }

    public function rejectRequest($id)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login');
        }

        $req = EmergencyRequest::findOrFail($id);
        $req->update(['status' => 'rejected']);

        return back()->with('success', 'Request #' . $id . ' rejected.');
    }

    public function completeRequest($id)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login');
        }

        $req = EmergencyRequest::findOrFail($id);
        $req->update(['status' => 'completed']);

        return back()->with('success', 'Request #' . $id . ' marked as completed.');
    }

    // ── Donation Actions ──────────────────────────────────
    public function scheduleDonation(Request $request)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login');
        }

        $request->validate([
            'donor_id' => 'required|exists:donors,donor_id',
            'patient_id' => 'nullable|exists:patients,patient_id',
            'req_id' => 'nullable|exists:emergency_requests,req_id',
            'date' => 'required|date|after_or_equal:today',
            'quantity_units' => 'required|integer|min:1|max:10',
        ]);

        Donation::create([
            'donor_id' => $request->donor_id,
            'hospital_id' => session('hospital_id'),
            'patient_id' => $request->patient_id,
            'req_id' => $request->req_id,
            'date' => $request->date,
            'quantity_units' => $request->quantity_units,
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Donation scheduled successfully.');
    }

    public function completeDonation($id)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login');
        }

        $donation = Donation::findOrFail($id);
        $donation->update(['status' => 'completed']);

        // Update donor's last_donated_at
        if ($donation->donor) {
            $donation->donor->update(['last_donated_at' => now()]);
        }

        // If linked to a request, mark request as completed too
        if ($donation->req_id) {
            EmergencyRequest::where('req_id', $donation->req_id)->update(['status' => 'completed']);
        }

        return back()->with('success', 'Donation marked as completed.');
    }

    public function cancelDonation($id)
    {
        if (!session()->has('hospital_id')) {
            return redirect()->route('hospital.login');
        }

        $donation = Donation::findOrFail($id);
        $donation->update(['status' => 'cancelled']);

        return back()->with('success', 'Donation cancelled.');
    }

    // ── Logout ────────────────────────────────────────────
    public function logout()
    {
        session()->forget(['hospital_id', 'hospital_name', 'hospital_email', 'role']);
        return redirect()->route('hospital.login')
                         ->with('success', 'Logged out successfully.');
    }
}