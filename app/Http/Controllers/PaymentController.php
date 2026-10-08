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

        return view('booking.payment', compact('booking'));
    }

    public function store(Request $request, $bookingId)
    {
        $booking = $this->customer()->bookings()->with('payments')->findOrFail($bookingId);
        $data = $request->validate([
            'payment_method' => ['required', 'in:qrCode,creditCard,bankTransfer'],
        ]);

        if ($booking->payments()->where('payment_status', 'completed')->exists()) {
            return redirect()->route('my-bookings.index')->with('success', 'รายการนี้ชำระเงินแล้ว');
        }

        $payment = $booking->payments()->where('payment_status', 'pending')->latest('payment_id')->first();
        if ($payment) {
            $payment->update([
                'payment_amount' => $booking->booking_total_price,
                'payment_method' => $data['payment_method'],
            ]);
        } else {
            $booking->payments()->create([
                'payment_amount' => $booking->booking_total_price,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
            ]);
        }

        return redirect()->route('my-bookings.index')->with('success', 'บันทึกคำขอจองแล้ว สถานะการชำระเงินกำลังรอตรวจสอบ');
    }

    private function customer(): Customer
    {
        abort_unless(auth()->user()->user_role === 'customer', 403);

        return Customer::firstOrCreate(['user_id' => auth()->id()]);
    }
}
