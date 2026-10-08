<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
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
    Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::put('/admin/bookings/{bookingId}/status', [AdminBookingController::class, 'updateStatus'])->name('admin.bookings.status');
    Route::get('/admin/customers', [AdminCustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/create', [AdminCustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/admin/customers', [AdminCustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{customerId}', [AdminCustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/admin/customers/{customerId}/edit', [AdminCustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/admin/customers/{customerId}', [AdminCustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{customerId}', [AdminCustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/admin/services', [AdminServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/admin/services/create', [AdminServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services', [AdminServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/admin/services/{serviceId}/edit', [AdminServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/admin/services/{serviceId}', [AdminServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/admin/services/{serviceId}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');
    Route::get('/admin/staff', [AdminStaffController::class, 'index'])->name('staff.index');
    Route::post('/admin/staff', [AdminStaffController::class, 'storeStaff'])->name('staff.store');
    Route::post('/admin/staff/attendance', [AdminStaffController::class, 'saveAttendance'])->name('staff.attendance.update');
    Route::post('/admin/staff/schedules', [AdminStaffController::class, 'storeSchedule'])->name('staff.schedules.store');
    Route::post('/admin/staff/closed-days', [AdminStaffController::class, 'toggleClosedDay'])->name('staff.closed-days.toggle');
    Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments');
    Route::get('/admin/payments/{paymentId}', [AdminPaymentController::class, 'show'])->name('admin.payments.show');
    Route::post('/admin/payments/{paymentId}/confirm', [AdminPaymentController::class, 'confirm'])->name('admin.payments.confirm');
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
