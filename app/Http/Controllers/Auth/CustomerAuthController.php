<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Map email to the actual database field
        $loginData = [
            'Email' => $credentials['email'],
            'password' => $credentials['password']
        ];
        
        if (Auth::guard('web')->attempt($loginData)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Login successful!');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:customers,email',
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

    $customer = Customer::create([
        'Name' => $request->name,
        'Email' => $request->email,
        'Password' => Hash::make($request->password),
    ]);

    Auth::guard('web')->login($customer);

    return redirect('/')->with('success', "Welcome to MobileHub, {$customer->Name}! Your account has been created successfully. You are now logged in and can start shopping for mobile phones.");
}

    public function signup(Request $request)
    {
     

          return view('customer.signup');
    }

    public function logout(Request $request)
    {
        $customerName = auth('web')->user()->Name ?? 'Customer';
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect(route('customer.login'))->with('success', "Goodbye {$customerName}! You have been logged out successfully.");
    }
}
