<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    // Show signup form
    public function showSignup()
    {
        return view('staff.signup');
    }

    // Handle signup form submit
    public function signup(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:staff,UserName',
            'staff_name' => 'required|string|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // At least one lowercase, uppercase, and digit
            ],
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $staff = Staff::create([
            'UserName' => $request->username,
            'StaffName' => $request->staff_name,
            'Password' => Hash::make($request->password),
        ]);

    Auth::guard('staff')->login($staff);

    return redirect()->route('staff.dashboard')->with('success', "Welcome to the MobileHub Staff Portal, {$staff->StaffName}! Your staff account has been created successfully. You now have access to the management dashboard.");
    }

    // Show login form
    public function showLogin()
    {
        return view('staff.login');
    }

    // Handle login form submit
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Map to the actual database field names
        $loginData = [
            'UserName' => $credentials['username'],
            'password' => $credentials['password']
        ];

        if (Auth::guard('staff')->attempt($loginData)) {
            $request->session()->regenerate();
            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    // Logout
    public function logout(Request $request)
    {
        $staffName = auth('staff')->user()->StaffName ?? 'Staff member';
        
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect(route('staff.login'))->with('success', "Goodbye {$staffName}! You have been logged out successfully.");
    }
}

