<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\SuperadminBookingController;
use App\Http\Controllers\SuperadminCustomerController;
use App\Http\Controllers\SuperadminPaymentController;
use App\Http\Controllers\SuperadminServiceController;
use App\Http\Controllers\SuperadminStaffController;
use App\Http\Controllers\SuperadminProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SuperadminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');

// ใช้ Controller ของเราเองแทน Fortify เฉพาะหน้า Login และ Register
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login.store');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::put('/bookings/{bookingId}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [AdminCustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [AdminCustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customerId}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{customerId}/edit', [AdminCustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customerId}', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customerId}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');
        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.index');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

    Route::prefix('superadmin')->name('superadmin.')->middleware('role:superadmin')->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [SuperadminBookingController::class, 'index'])->name('bookings.index');
        Route::put('/bookings/{bookingId}/status', [SuperadminBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::get('/customers', [SuperadminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [SuperadminCustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [SuperadminCustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customerId}', [SuperadminCustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{customerId}/edit', [SuperadminCustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customerId}', [SuperadminCustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customerId}', [SuperadminCustomerController::class, 'destroy'])->name('customers.destroy');
        Route::get('/services', [SuperadminServiceController::class, 'index'])->name('services.index');
        Route::get('/services/create', [SuperadminServiceController::class, 'create'])->name('services.create');
        Route::post('/services', [SuperadminServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{serviceId}/edit', [SuperadminServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{serviceId}', [SuperadminServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{serviceId}', [SuperadminServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/staff', [SuperadminStaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [SuperadminStaffController::class, 'storeStaff'])->name('staff.store');
        Route::post('/staff/attendance', [SuperadminStaffController::class, 'saveAttendance'])->name('staff.attendance.update');
        Route::post('/staff/schedules', [SuperadminStaffController::class, 'storeSchedule'])->name('staff.schedules.store');
        Route::post('/staff/closed-days', [SuperadminStaffController::class, 'toggleClosedDay'])->name('staff.closed-days.toggle');
        Route::get('/profile', [SuperadminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [SuperadminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [SuperadminProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::get('/payments', [SuperadminPaymentController::class, 'index'])->name('payments');
        Route::get('/payments/{paymentId}', [SuperadminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{paymentId}/confirm', [SuperadminPaymentController::class, 'confirm'])->name('payments.confirm');
        Route::post('/payments/{paymentId}/reject', [SuperadminPaymentController::class, 'reject'])->name('payments.reject');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings.index');
    Route::post('/my-bookings/{bookingId}/cancel', [BookingController::class, 'cancelMyBooking'])->name('my-bookings.cancel');

    Route::get('/booking/{serviceId}', [BookingController::class, 'show'])->name('booking.show');
    Route::post('/booking/{serviceId}/pet', [BookingController::class, 'choosePet'])->name('booking.pet');
    Route::get('/booking/{serviceId}/schedule', [BookingController::class, 'schedule'])->name('booking.schedule');
    Route::post('/booking/{serviceId}/schedule', [BookingController::class, 'chooseSchedule'])->name('booking.schedule.store');
    Route::get('/booking/{serviceId}/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/booking/{serviceId}', [BookingController::class, 'store'])->name('booking.store');

    Route::get('/payments/{bookingId}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{bookingId}', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
    Route::get('/pets/create/form', [PetController::class, 'createForm'])->name('pets.create.form');
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{id}/edit', [PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{id}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');
});
require __DIR__.'/settings.php';
