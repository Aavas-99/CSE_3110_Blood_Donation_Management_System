<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HospitalController extends Controller
{
    // ── Auth check helper ─────────────────────────────
    private function authCheck()
    {
        return session()->has('hospital_id');
    }

    private function redirectToLogin(string $message = 'Please login to access the dashboard.')
    {
        return redirect()->route('hospital.login')->with('error', $message);
    }

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

        DB::insert(
            'INSERT INTO hospitals (name, email, password, phone, district, address, website, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)',
            [
                $request->name,
                $request->email,
                Hash::make($request->password),
                $request->phone,
                $request->district,
                $request->address,
                $request->website,
                now(),                          // FIX: was SYSTIMESTAMP (Oracle-only)
            ]
        );

        return redirect()->route('hospital.login')
                         ->with('success', 'Hospital registered successfully! Please sign in.');
    }

    // ── Login ─────────────────────────────────────────────
    public function showLogin()
    {
        if ($this->authCheck()) {
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

        $hospital = DB::selectOne(
            'SELECT hospital_id, name, email, password FROM hospitals WHERE email = ?',
            [$request->email]
        );

        if (!$hospital || !Hash::check($request->password, $hospital->password)) {
            return back()
                ->withInput(['email' => $request->email])
                ->with('error', 'Invalid email or password. Please try again.');
        }

        session([
            'hospital_id'    => $hospital->hospital_id,
            'hospital_name'  => $hospital->name,
            'hospital_email' => $hospital->email,
            'role'           => 'hospital',
        ]);

        return redirect()->route('hospital.dashboard')
                         ->with('success', 'Welcome back, ' . $hospital->name . '!');
    }

    // ── Dashboard ─────────────────────────────────────────
    public function dashboard(Request $request)
    {
        if (!$this->authCheck()) {
            return $this->redirectToLogin();
        }

        $hospitalId = session('hospital_id');

        // ── Stats ────────────────────────────────────────
        // FIX: scope totalRequests to this hospital via donations linkage,
        // OR keep global if hospital sees all requests — keeping global here
        // but scoping totalDonations and totalPatientsServed to this hospital.
        $totalRequests = DB::selectOne(
            'SELECT COUNT(*) as count FROM emergency_requests'
        )->count;

        $totalDonations = DB::selectOne(
            'SELECT COUNT(*) as count FROM donations WHERE hospital_id = ?',
            [$hospitalId]
        )->count;

        $totalPatientsServed = DB::selectOne(
            'SELECT COUNT(DISTINCT patient_id) as count FROM donations WHERE hospital_id = ? AND patient_id IS NOT NULL',
            [$hospitalId]
        )->count;

        // ── Request status summary ────────────────────────
        $requestStatusSummaryRaw = DB::select(
            'SELECT status, COUNT(*) as count FROM emergency_requests GROUP BY status'
        );
        $requestStatusSummary = [];
        foreach ($requestStatusSummaryRaw as $row) {
            $requestStatusSummary[$row->status] = $row->count;
        }

        // ── Blood Requests query ──────────────────────────
        $reqSql    = 'SELECT er.*, p.name as patient_name, p.phone as patient_phone
                      FROM emergency_requests er
                      LEFT JOIN patients p ON er.patient_id = p.patient_id
                      WHERE 1=1';
        $reqParams = [];

        if ($request->filled('req_search')) {
            $search     = '%' . $request->req_search . '%';
            $reqSql    .= ' AND (CAST(er.req_id AS CHAR) LIKE ? OR p.name LIKE ?)';
            $reqParams[] = $search;
            $reqParams[] = $search;
        }

        if ($request->filled('req_status') && $request->req_status !== 'all') {
            $reqSql    .= ' AND er.status = ?';
            $reqParams[] = $request->req_status;
        }

        if ($request->filled('req_urgency') && $request->req_urgency !== 'all') {
            $reqSql    .= ' AND er.urgency_level = ?';
            $reqParams[] = $request->req_urgency;
        }

        $reqSort = $request->req_sort ?? 'newest';
        switch ($reqSort) {
            case 'urgency':
                $reqSql .= " ORDER BY CASE er.urgency_level WHEN 'critical' THEN 3 WHEN 'urgent' THEN 2 ELSE 1 END DESC";
                break;
            case 'units_desc':
                $reqSql .= ' ORDER BY er.quantity_units DESC';
                break;
            case 'units_asc':
                $reqSql .= ' ORDER BY er.quantity_units ASC';
                break;
            default:
                $reqSql .= ' ORDER BY er.created_at DESC';
        }

        $requests = DB::select($reqSql, $reqParams);

        // ── Donors query ──────────────────────────────────
        $donorSql    = 'SELECT d.* FROM donors d WHERE 1=1';
        $donorParams = [];

        if ($request->filled('donor_search')) {
            $search       = '%' . $request->donor_search . '%';
            $donorSql    .= ' AND (d.name LIKE ? OR d.phone LIKE ? OR d.email LIKE ? OR d.blood_group LIKE ?)';
            $donorParams[] = $search;
            $donorParams[] = $search;
            $donorParams[] = $search;
            $donorParams[] = $search;
        }

        if ($request->filled('donor_blood') && $request->donor_blood !== 'all') {
            $donorSql    .= ' AND d.blood_group = ?';
            $donorParams[] = $request->donor_blood;
        }

        if ($request->filled('donor_district') && $request->donor_district !== 'all') {
            $donorSql    .= ' AND d.district = ?';
            $donorParams[] = $request->donor_district;
        }

        if ($request->filled('donor_status') && $request->donor_status !== 'all') {
            $donorSql    .= ' AND d.status = ?';
            $donorParams[] = $request->donor_status;
        }

        $donorSort = $request->donor_sort ?? 'name_asc';
        switch ($donorSort) {
            case 'last_donation':
                $donorSql .= ' ORDER BY d.last_donated_at DESC';    // FIX: removed "NULLS LAST" (MySQL-incompatible)
                break;
            case 'name_desc':
                $donorSql .= ' ORDER BY d.name DESC';
                break;
            default:
                $donorSql .= ' ORDER BY d.name ASC';
        }

        $donors = DB::select($donorSql, $donorParams);

        // ── Patients query ────────────────────────────────
        $patientSql    = 'SELECT p.*,
                           (SELECT COUNT(*) FROM emergency_requests WHERE patient_id = p.patient_id) as request_count
                           FROM patients p
                           WHERE 1=1';
        $patientParams = [];

        if ($request->filled('patient_search')) {
            $search         = '%' . $request->patient_search . '%';
            $patientSql    .= ' AND (p.name LIKE ? OR p.phone LIKE ? OR p.email LIKE ?)';
            $patientParams[] = $search;
            $patientParams[] = $search;
            $patientParams[] = $search;
        }

        if ($request->filled('patient_blood') && $request->patient_blood !== 'all') {
            $patientSql    .= ' AND p.blood_group = ?';
            $patientParams[] = $request->patient_blood;
        }

        if ($request->filled('patient_district') && $request->patient_district !== 'all') {
            $patientSql    .= ' AND p.district = ?';
            $patientParams[] = $request->patient_district;
        }

        $patients = DB::select($patientSql, $patientParams);

        // ── Donations query ───────────────────────────────
        $donationSql    = 'SELECT dn.*,
                            dr.name       as donor_name,
                            dr.blood_group as donor_blood,
                            p.name        as patient_name,
                            p.blood_group as patient_blood,
                            er.req_id     as request_id
                            FROM donations dn
                            LEFT JOIN donors dr      ON dn.donor_id   = dr.donor_id
                            LEFT JOIN patients p     ON dn.patient_id = p.patient_id
                            LEFT JOIN emergency_requests er ON dn.req_id = er.req_id
                            WHERE dn.hospital_id = ?';
        $donationParams = [$hospitalId];

        if ($request->filled('donation_search')) {
            $search          = '%' . $request->donation_search . '%';
            $donationSql    .= ' AND (dr.name LIKE ? OR p.name LIKE ?)';
            $donationParams[] = $search;
            $donationParams[] = $search;
        }

        if ($request->filled('donation_status') && $request->donation_status !== 'all') {
            $donationSql    .= ' AND dn.status = ?';
            $donationParams[] = $request->donation_status;
        }

        $donationSort = $request->donation_sort ?? 'newest';
        switch ($donationSort) {
            case 'oldest':
                $donationSql .= ' ORDER BY dn.donation_date ASC';
                break;
            case 'quantity_desc':
                $donationSql .= ' ORDER BY dn.quantity_units DESC';
                break;
            case 'quantity_asc':
                $donationSql .= ' ORDER BY dn.quantity_units ASC';
                break;
            default:
                $donationSql .= ' ORDER BY dn.donation_date DESC';
        }

        $donations = DB::select($donationSql, $donationParams);

        // ── Reference data ────────────────────────────────
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $districts   = [
            'Bagerhat','Bandarban','Barguna','Barishal','Bhola','Bogura',
            'Brahmanbaria','Chandpur','Chapai Nawabganj','Chattogram','Chuadanga','Cox\'s Bazar',
            'Cumilla','Dhaka','Dinajpur','Faridpur','Feni','Gaibandha','Gazipur','Gopalganj',
            'Habiganj','Jamalpur','Jessore','Jhalokathi','Jhenaidah','Joypurhat','Khagrachari',
            'Khulna','Kishoreganj','Kurigram','Kushtia','Lakshmipur','Lalmonirhat','Madaripur',
            'Magura','Manikganj','Meherpur','Moulvibazar','Munshiganj','Mymensingh','Naogaon',
            'Narail','Narayanganj','Narsingdi','Natore','Netrokona','Nilphamari','Noakhali',
            'Pabna','Panchagarh','Patuakhali','Pirojpur','Rajbari','Rajshahi','Rangamati',
            'Rangpur','Satkhira','Shariatpur','Sherpur','Sirajganj','Sunamganj','Sylhet',
            'Tangail','Thakurgaon',
        ];

        return view('hospital.dashboard', compact(
            'totalRequests', 'totalDonations', 'totalPatientsServed', 'requestStatusSummary',
            'requests', 'donors', 'patients', 'donations',
            'bloodGroups', 'districts'
        ));
    }

    // ── Request actions ───────────────────────────────────
    public function approveRequest($id)
    {
        if (!$this->authCheck()) return $this->redirectToLogin();

        DB::update(
            'UPDATE emergency_requests SET status = ?, updated_at = ? WHERE req_id = ?',
            ['approved', now(), $id]          // FIX: was SYSTIMESTAMP
        );

        return back()->with('success', 'Request #' . $id . ' approved successfully.');
    }

    public function rejectRequest($id)
    {
        if (!$this->authCheck()) return $this->redirectToLogin();

        DB::update(
            'UPDATE emergency_requests SET status = ?, updated_at = ? WHERE req_id = ?',
            ['rejected', now(), $id]          // FIX: was SYSTIMESTAMP
        );

        return back()->with('success', 'Request #' . $id . ' rejected.');
    }

    public function completeRequest($id)
    {
        if (!$this->authCheck()) return $this->redirectToLogin();

        DB::update(
            'UPDATE emergency_requests SET status = ?, updated_at = ? WHERE req_id = ?',
            ['completed', now(), $id]         // FIX: was SYSTIMESTAMP
        );

        return back()->with('success', 'Request #' . $id . ' marked as completed.');
    }

    // ── Donation actions ──────────────────────────────────
    public function scheduleDonation(Request $request)
    {
        if (!$this->authCheck()) return $this->redirectToLogin();

        $request->validate([
            'donor_id'       => 'required|exists:donors,donor_id',
            'patient_id'     => 'nullable|exists:patients,patient_id',
            'req_id'         => 'nullable|exists:emergency_requests,req_id',
            'donation_date'  => 'required|date|after_or_equal:today',  // FIX: was 'date' key
            'quantity_units' => 'required|integer|min:1|max:10',
        ]);

        DB::insert(
            'INSERT INTO donations (donor_id, hospital_id, patient_id, req_id, donation_date, quantity_units, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)',
            [
                $request->donor_id,
                session('hospital_id'),
                $request->patient_id ?: null,
                $request->req_id     ?: null,
                $request->donation_date,        // FIX: was $request->date (wrong field name)
                $request->quantity_units,
                'scheduled',
                now(),                          // FIX: was SYSTIMESTAMP
            ]
        );

        return back()->with('success', 'Donation scheduled successfully.');
    }

    public function completeDonation($id)
    {
        if (!$this->authCheck()) return $this->redirectToLogin();

        $donation = DB::selectOne(
            'SELECT donor_id, req_id FROM donations WHERE donation_id = ?',
            [$id]
        );

        if (!$donation) {
            return back()->with('error', 'Donation not found.');  // FIX: added null guard
        }

        DB::update(
            'UPDATE donations SET status = ?, updated_at = ? WHERE donation_id = ?',
            ['completed', now(), $id]          // FIX: was SYSTIMESTAMP
        );

        DB::update(
            'UPDATE donors SET last_donated_at = ?, updated_at = ? WHERE donor_id = ?',
            [now(), now(), $donation->donor_id] // FIX: was SYSTIMESTAMP
        );

        if ($donation->req_id) {
            DB::update(
                'UPDATE emergency_requests SET status = ?, updated_at = ? WHERE req_id = ?',
                ['completed', now(), $donation->req_id]  // FIX: was SYSTIMESTAMP
            );
        }

        return back()->with('success', 'Donation marked as completed.');
    }

    public function cancelDonation($id)
    {
        if (!$this->authCheck()) return $this->redirectToLogin();

        DB::update(
            'UPDATE donations SET status = ?, updated_at = ? WHERE donation_id = ?',
            ['cancelled', now(), $id]          // FIX: was SYSTIMESTAMP
        );

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