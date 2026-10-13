<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->user_role === 'admin') {
            return redirect()->route('admin_dashboard');
        }
        if (Auth::user()->user_role === 'superadmin') {
            return redirect()->route('superadmin_dashboard');
        }

        return redirect()->route('home');
    }
}
