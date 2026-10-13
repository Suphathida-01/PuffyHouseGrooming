<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    $profile = function () {
        $route = match (auth()->user()->user_role) {
            'admin' => 'admin_profile_edit',
            'superadmin' => 'superadmin_profile_edit',
            default => 'profile_edit',
        };

        return redirect()->route($route);
    };

    Route::get('settings', $profile)->name('settings_index');
    Route::get('settings/profile', $profile)->name('settings_profile');
    Route::get('settings/appearance', $profile)->name('appearance_edit');
    Route::get('settings/security', $profile)->name('security_edit');
});
