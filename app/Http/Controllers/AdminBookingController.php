<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->user_role !== 'admin') {
            return redirect()->route('home');
        }

        $status = $request->query('status', 'all');
        if (! in_array($status, ['all', 'pending', 'confirmed', 'completed', 'cancelled'], true)) {
            $status = 'all';
        }

        $date = $request->query('date', '');
        if ($date !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = '';
        }

        $sql = "SELECT bookings.booking_id, bookings.booking_date, bookings.booking_start_time,
                bookings.booking_status, customers_user.name AS customer_name, pets.pet_name,
                services.service_name, admins_user.name AS admin_name
            FROM bookings
            JOIN customers ON customers.customer_id = bookings.customer_id
            JOIN users AS customers_user ON customers_user.id = customers.user_id
            JOIN pets ON pets.id = bookings.pet_id
            JOIN services ON services.service_id = bookings.service_id
            LEFT JOIN admins ON admins.admin_id = bookings.admin_id
            LEFT JOIN users AS admins_user ON admins_user.id = admins.user_id
            WHERE bookings.deleted_at IS NULL";
        $bindings = [];

        if ($status !== 'all') {
            $sql .= ' AND bookings.booking_status = ?';
            $bindings[] = $status;
        }
        if ($date !== '') {
            $sql .= ' AND DATE(bookings.booking_date) = ?';
            $bindings[] = $date;
        }

        $sql .= ' ORDER BY bookings.booking_date DESC, bookings.booking_start_time DESC';
        $bookings = DB::select($sql, $bindings);

        return view('admin.bookings', compact('bookings', 'status', 'date'));
    }

    public function updateStatus(Request $request, $bookingId)
    {
        if (Auth::user()->user_role !== 'admin') {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'booking_status' => ['required', 'in:confirmed,completed,cancelled'],
        ]);
        $booking = DB::select('SELECT booking_status FROM bookings WHERE booking_id = ? AND deleted_at IS NULL', [$bookingId]);
        abort_if(count($booking) === 0, 404);

        $currentStatus = $booking[0]->booking_status;
        $newStatus = $data['booking_status'];
        $allowed = ($currentStatus === 'pending' && in_array($newStatus, ['confirmed', 'cancelled'], true))
            || ($currentStatus === 'confirmed' && in_array($newStatus, ['completed', 'cancelled'], true));

        if (! $allowed) {
            return back()->with('booking_error', 'ไม่สามารถเปลี่ยนสถานะรายการนี้ได้');
        }

        DB::update('UPDATE bookings SET booking_status = ?, updated_at = ? WHERE booking_id = ?', [$newStatus, now(), $bookingId]);

        return back()->with('success', 'อัปเดตสถานะการจองแล้ว');
    }
}
