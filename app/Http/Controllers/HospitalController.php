<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HospitalController extends Controller
{
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
                now(),
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

    // ── Dashboard (Donors only, no filters) ───────────────
    public function dashboard()
    {
        if (!$this->authCheck()) {
            return $this->redirectToLogin();
        }

        $donors = DB::select('SELECT * FROM donors ORDER BY name ASC');

        return view('hospital.dashboard', compact('donors'));
    }

    // ── Logout ────────────────────────────────────────────
    public function logout()
    {
        session()->forget(['hospital_id', 'hospital_name', 'hospital_email', 'role']);
        return redirect()->route('hospital.login')
                         ->with('success', 'Logged out successfully.');
    }
}