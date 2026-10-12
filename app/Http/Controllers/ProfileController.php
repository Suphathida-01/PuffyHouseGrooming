<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        abort_unless(request()->user()->user_role === 'customer', 403);
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->user_role === 'customer', 403);
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'user_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($data);

        return redirect()->route('profile.edit')->with('profile_success', 'บันทึกข้อมูลส่วนตัวแล้ว');
    }

    public function updatePassword(Request $request)
    {
        abort_unless($request->user()->user_role === 'customer', 403);
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return redirect()->route('profile.edit')->with('password_success', 'เปลี่ยนรหัสผ่านแล้ว');
    }
}
