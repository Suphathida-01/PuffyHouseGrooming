<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create($bookingId)
    {
        $booking = $this->customer()->bookings()
            ->with(['pet.breed.petType', 'service', 'payments'])
            ->findOrFail($bookingId);
        abort_unless($booking->booking_status === 'pending', 403);

        return view('booking.payment', compact('booking'));
    }

    public function store(Request $request, $bookingId)
    {
        $booking = $this->customer()->bookings()->with('payments')->findOrFail($bookingId);
        abort_unless($booking->booking_status === 'pending', 403);
        $data = $request->validate([
            'payment_method' => ['required', 'in:qrCode,creditCard,bankTransfer'],
        ]);

        if ($booking->payments()->where('payment_status', 'completed')->exists()) {
            return redirect()->route('my-bookings.index')->with('success', 'รายการนี้ชำระเงินแล้ว');
        }

        $paymentStatus = $data['payment_method'] === 'qrCode' ? 'pending' : 'completed';
        $payment = $booking->payments()->where('payment_status', 'pending')->latest('payment_id')->first();
        if ($payment) {
            $payment->update([
                'payment_amount' => $booking->booking_total_price,
                'payment_method' => $data['payment_method'],
                'payment_status' => $paymentStatus,
                'payment_date' => now(),
            ]);
        } else {
            $booking->payments()->create([
                'payment_amount' => $booking->booking_total_price,
                'payment_method' => $data['payment_method'],
                'payment_status' => $paymentStatus,
                'payment_date' => now(),
            ]);
        }

        $message = $paymentStatus === 'pending'
            ? 'บันทึกคำขอจองแล้ว สถานะการชำระเงินกำลังรอตรวจสอบ'
            : 'บันทึกสถานะการชำระเงินเป็นชำระแล้ว';

        return redirect()->route('my-bookings.index')->with('success', $message);
    }

    private function customer(): Customer
    {
        abort_unless(auth()->user()->user_role === 'customer', 403);

        return Customer::firstOrCreate(['user_id' => auth()->id()]);
    }
}
