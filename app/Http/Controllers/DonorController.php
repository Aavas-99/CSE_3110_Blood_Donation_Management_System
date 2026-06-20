<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DonorController extends Controller
{
    // ── Register 
    public function showRegister()
    {
        $hospitals = Hospital::orderBy('name')->get();
        return view('donor.register', compact('hospitals'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:150',
            'email'         => 'required|email|unique:donors,email',
            'phone'         => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:' . now()->subYears(18)->format('Y-m-d'),
            'blood_group'   => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'gender'        => 'required|in:Male,Female,Other',
            'district'      => 'required|string|max:100',
            'address'       => 'required|string',
            'hospital_id'   => 'required|exists:hospitals,hospital_id',
            'status'        => 'required|in:available,unavailable',
            'last_donated_at' => 'nullable|date|before_or_equal:today',
            'password' => [
                'required', 'string', 'min:8', 'max:64', 'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&^_\-]/',
            ],
        ], [
            'email.unique'              => 'This email is already registered.',
            'password.confirmed'        => 'Passwords do not match.',
            'password.regex'            => 'Password must contain uppercase, lowercase, number, and special character.',
            'date_of_birth.before'      => 'You must be at least 18 years old to register as a donor.',
            'hospital_id.exists'        => 'Selected hospital is invalid.',
        ]);

        Donor::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => $request->password,
            'phone'         => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'blood_group'   => $request->blood_group,
            'gender'        => $request->gender,
            'district'      => $request->district,
            'address'       => $request->address,
            'hospital_id'   => $request->hospital_id,
            'status'        => $request->status,
            'last_donated_at' => $request->last_donated_at,
        ]);

        return redirect()->route('donor.login')
                         ->with('success', 'Donor registered successfully! Please sign in.');
    }

    // ── Login 
    public function showLogin()
    {
        if (session()->has('donor_id')) {
            return redirect()->route('donor.dashboard');
        }
        return view('donor.login');
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

        $donor = Donor::where('email', $request->email)->first();

        if (!$donor || !Hash::check($request->password, $donor->password)) {
            return back()
                ->withInput(['email' => $request->email])
                ->with('error', 'Invalid email or password. Please try again.');
        }

        session([
            'donor_id'   => $donor->donor_id,
            'donor_name' => $donor->name,
            'donor_email'=> $donor->email,
            'role'       => 'donor',
        ]);

        return redirect()->route('donor.dashboard')
                         ->with('success', 'Welcome back, ' . $donor->name . '!');
    }

    // ── Dashboard (placeholder) 
    public function dashboard()
    {
        if (!session()->has('donor_id')) {
            return redirect()->route('donor.login')
                             ->with('error', 'Please login to access the dashboard.');
        }
        return view('donor.dashboard');
    }

    // ── Logout 
    public function logout()
    {
        session()->forget(['donor_id', 'donor_name', 'donor_email', 'role']);
        return redirect()->route('donor.login')
                         ->with('success', 'Logged out successfully.');
    }
}