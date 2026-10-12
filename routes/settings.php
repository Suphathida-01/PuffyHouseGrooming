<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    $profile = function () {
        $route = match (auth()->user()->user_role) {
            'admin' => 'admin.profile.edit',
            'superadmin' => 'superadmin.profile.edit',
            default => 'profile.edit',
        };

        return redirect()->route($route);
    };

    Route::get('settings', $profile)->name('settings.index');
    Route::get('settings/profile', $profile)->name('settings.profile');
    Route::get('settings/appearance', $profile)->name('appearance.edit');
    Route::get('settings/security', $profile)->name('security.edit');
});
