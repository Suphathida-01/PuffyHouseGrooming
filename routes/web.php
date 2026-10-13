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
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services_show');

Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login_store');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register_store');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->name('admin_')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings_index');
        Route::put('/bookings/{bookingId}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings_status');
        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers_index');
        Route::get('/customers/create', [AdminCustomerController::class, 'create'])->name('customers_create');
        Route::post('/customers', [AdminCustomerController::class, 'store'])->name('customers_store');
        Route::get('/customers/{customerId}', [AdminCustomerController::class, 'show'])->name('customers_show');
        Route::get('/customers/{customerId}/edit', [AdminCustomerController::class, 'edit'])->name('customers_edit');
        Route::put('/customers/{customerId}', [AdminCustomerController::class, 'update'])->name('customers_update');
        Route::delete('/customers/{customerId}', [AdminCustomerController::class, 'destroy'])->name('customers_destroy');
        Route::get('/services', [AdminServiceController::class, 'index'])->name('services_index');
        Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff_index');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile_edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile_update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile_password_update');
    });

    Route::prefix('superadmin')->name('superadmin_')->middleware('role:superadmin')->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [SuperadminBookingController::class, 'index'])->name('bookings_index');
        Route::put('/bookings/{bookingId}/status', [SuperadminBookingController::class, 'updateStatus'])->name('bookings_status');
        Route::get('/customers', [SuperadminCustomerController::class, 'index'])->name('customers_index');
        Route::get('/customers/create', [SuperadminCustomerController::class, 'create'])->name('customers_create');
        Route::post('/customers', [SuperadminCustomerController::class, 'store'])->name('customers_store');
        Route::get('/customers/{customerId}', [SuperadminCustomerController::class, 'show'])->name('customers_show');
        Route::get('/customers/{customerId}/edit', [SuperadminCustomerController::class, 'edit'])->name('customers_edit');
        Route::put('/customers/{customerId}', [SuperadminCustomerController::class, 'update'])->name('customers_update');
        Route::delete('/customers/{customerId}', [SuperadminCustomerController::class, 'destroy'])->name('customers_destroy');
        Route::get('/services', [SuperadminServiceController::class, 'index'])->name('services_index');
        Route::get('/services/create', [SuperadminServiceController::class, 'create'])->name('services_create');
        Route::post('/services', [SuperadminServiceController::class, 'store'])->name('services_store');
        Route::get('/services/{serviceId}/edit', [SuperadminServiceController::class, 'edit'])->name('services_edit');
        Route::put('/services/{serviceId}', [SuperadminServiceController::class, 'update'])->name('services_update');
        Route::delete('/services/{serviceId}', [SuperadminServiceController::class, 'destroy'])->name('services_destroy');
        Route::get('/staff', [SuperadminStaffController::class, 'index'])->name('staff_index');
        Route::post('/staff', [SuperadminStaffController::class, 'storeStaff'])->name('staff_store');
        Route::post('/staff/attendance', [SuperadminStaffController::class, 'saveAttendance'])->name('staff_attendance_update');
        Route::post('/staff/schedules', [SuperadminStaffController::class, 'storeSchedule'])->name('staff_schedules_store');
        Route::post('/staff/closed-days', [SuperadminStaffController::class, 'toggleClosedDay'])->name('staff_closed-days_toggle');
        Route::get('/profile', [SuperadminProfileController::class, 'edit'])->name('profile_edit');
        Route::put('/profile', [SuperadminProfileController::class, 'update'])->name('profile_update');
        Route::put('/profile/password', [SuperadminProfileController::class, 'updatePassword'])->name('profile_password_update');
        Route::get('/payments', [SuperadminPaymentController::class, 'index'])->name('payments');
        Route::get('/payments/{paymentId}', [SuperadminPaymentController::class, 'show'])->name('payments_show');
        Route::post('/payments/{paymentId}/confirm', [SuperadminPaymentController::class, 'confirm'])->name('payments_confirm');
        Route::post('/payments/{paymentId}/reject', [SuperadminPaymentController::class, 'reject'])->name('payments_reject');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile_edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile_update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile_password_update');

    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings_index');
    Route::post('/my-bookings/{bookingId}/cancel', [BookingController::class, 'cancelMyBooking'])->name('my-bookings_cancel');

    Route::get('/booking/{serviceId}', [BookingController::class, 'show'])->name('booking_show');
    Route::post('/booking/{serviceId}/pet', [BookingController::class, 'choosePet'])->name('booking_pet');
    Route::get('/booking/{serviceId}/schedule', [BookingController::class, 'schedule'])->name('booking_schedule');
    Route::post('/booking/{serviceId}/schedule', [BookingController::class, 'chooseSchedule'])->name('booking_schedule_store');
    Route::get('/booking/{serviceId}/confirm', [BookingController::class, 'confirm'])->name('booking_confirm');
    Route::post('/booking/{serviceId}', [BookingController::class, 'store'])->name('booking_store');

    Route::get('/payments/{bookingId}', [PaymentController::class, 'create'])->name('payments_create');
    Route::post('/payments/{bookingId}', [PaymentController::class, 'store'])->name('payments_store');

    Route::get('/pets', [PetController::class, 'index'])->name('pets_index');
    Route::get('/pets/create', [PetController::class, 'create'])->name('pets_create');
    Route::get('/pets/create/form', [PetController::class, 'createForm'])->name('pets_create_form');
    Route::post('/pets', [PetController::class, 'store'])->name('pets_store');
    Route::get('/pets/{id}/edit', [PetController::class, 'edit'])->name('pets_edit');
    Route::put('/pets/{id}', [PetController::class, 'update'])->name('pets_update');
    Route::delete('/pets/{id}', [PetController::class, 'destroy'])->name('pets_destroy');
});
require __DIR__.'/settings.php';
