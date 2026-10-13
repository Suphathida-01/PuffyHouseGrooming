<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->user_role !== 'admin') {
            return redirect()->route('home');
        }

        $status = $request->query('status', 'all');
        if (! in_array($status, ['all', 'pending', 'cancelled', 'completed'], true)) {
            $status = 'all';
        }

        $date = $request->query('date', '');
        if ($date !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = '';
        }

        $sortTime = now();
        $bookings = Booking::query()
            ->with([
                'customer' => fn ($query) => $query->withTrashed()->with(['user' => fn ($userQuery) => $userQuery->withTrashed()]),
                'pet' => fn ($query) => $query->withTrashed(),
                'service' => fn ($query) => $query->withTrashed(),
                'admin' => fn ($query) => $query->withTrashed()->with(['user' => fn ($userQuery) => $userQuery->withTrashed()]),
            ])
            ->whereHas('payments', fn ($query) => $query->where('payment_status', 'completed'))
            ->when($status !== 'all', fn ($query) => $query->where('booking_status', $status))
            ->when($date !== '', fn ($query) => $query->whereDate('booking_date', $date))
            ->get()
            ->sort(function (Booking $first, Booking $second) use ($sortTime) {
                $firstStart = Carbon::parse($first->booking_date->format('Y-m-d').' '.$first->booking_start_time);
                $secondStart = Carbon::parse($second->booking_date->format('Y-m-d').' '.$second->booking_start_time);
                $firstIsUpcoming = $firstStart->greaterThanOrEqualTo($sortTime);
                $secondIsUpcoming = $secondStart->greaterThanOrEqualTo($sortTime);

                if ($firstIsUpcoming !== $secondIsUpcoming) {
                    return $firstIsUpcoming ? -1 : 1;
                }

                return $firstIsUpcoming
                    ? $firstStart->getTimestamp() <=> $secondStart->getTimestamp()
                    : $secondStart->getTimestamp() <=> $firstStart->getTimestamp();
            })
            ->values();

        return view('admin.bookings', compact('bookings', 'status', 'date'));
    }

    public function updateStatus(Request $request, $bookingId)
    {
        if (Auth::user()->user_role !== 'admin') {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'booking_status' => ['required', 'in:completed,cancelled'],
        ]);
        $booking = Booking::query()
            ->whereKey($bookingId)
            ->whereHas('payments', fn ($query) => $query->where('payment_status', 'completed'))
            ->firstOrFail();

        $currentStatus = $booking->booking_status;
        $newStatus = $data['booking_status'];
        $allowed = $currentStatus === 'pending' && in_array($newStatus, ['completed', 'cancelled'], true);

        if (! $allowed) {
            return back()->with('booking_error', 'ไม่สามารถเปลี่ยนสถานะรายการนี้ได้');
        }

        $booking->update(['booking_status' => $newStatus]);

        return back()->with('success', 'อัปเดตสถานะการจองแล้ว');
    }
}