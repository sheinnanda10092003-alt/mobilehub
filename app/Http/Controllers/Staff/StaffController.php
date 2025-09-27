<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.welcome'); // loads the Blade view staff/welcome.blade.php
    }
}
