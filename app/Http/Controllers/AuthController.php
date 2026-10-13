<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])
                ->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->user_role === 'admin') {
            return redirect()->route('admin_dashboard');
        }
        if ($user->user_role === 'superadmin') {
            return redirect()->route('superadmin_dashboard');
        }

        return redirect()->route('home');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_phone' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        Customer::create(['user_id' => $user->id]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
