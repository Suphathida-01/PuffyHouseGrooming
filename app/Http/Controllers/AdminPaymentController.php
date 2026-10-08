<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    public function show($paymentId)
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);

        $payment = Payment::with(['booking.customer.user', 'booking.service', 'booking.pet.breed.petType'])
            ->findOrFail($paymentId);

        return view('admin.payment-show', compact('payment'));
    }

    public function index(Request $request)
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);

        $selectedDate = $request->query('date', today()->toDateString());
        $dateParts = explode('-', $selectedDate);
        $dateIsValid = count($dateParts) === 3
            && checkdate((int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[0]);
        if (! $dateIsValid) {
            $selectedDate = today()->toDateString();
        }

        $selectedStatus = $request->query('status', 'all');
        if (! in_array($selectedStatus, ['all', 'completed', 'pending', 'failed'], true)) {
            $selectedStatus = 'all';
        }

        $dailyCompletedPayments = Payment::where('payment_status', 'completed')
            ->whereDate('payment_date', $selectedDate);
        $summary = [
            'dailyRevenue' => (float) (clone $dailyCompletedPayments)->sum('payment_amount'),
            'dailyCompleted' => (clone $dailyCompletedPayments)->count(),
            'pendingCount' => Payment::where('payment_status', 'pending')->count(),
            'pendingTotal' => (float) Payment::where('payment_status', 'pending')->sum('payment_amount'),
        ];

        $paymentsQuery = Payment::with(['booking.customer.user', 'booking.service'])
            ->whereDate('payment_date', $selectedDate)
            ->orderByDesc('payment_date')->orderByDesc('payment_id');
        if ($selectedStatus !== 'all') {
            $paymentsQuery->where('payment_status', $selectedStatus);
        }
        $payments = $paymentsQuery->get();

        return view('payments-manage', compact('payments', 'summary', 'selectedDate', 'selectedStatus'));
    }

    public function confirm($paymentId)
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);

        $confirmed = DB::transaction(function () use ($paymentId) {
            $payment = Payment::lockForUpdate()->findOrFail($paymentId);
            if ($payment->payment_status !== 'pending') {
                return false;
            }

            $payment->update([
                'payment_status' => 'completed',
                'payment_date' => now(),
            ]);

            return true;
        });

        if (! $confirmed) {
            return back()->with('payment_error', 'รายการนี้ไม่ได้อยู่ในสถานะรอชำระแล้ว');
        }

        return redirect()->route('admin.payments', [
            'date' => today()->toDateString(),
            'status' => 'completed',
        ])->with('success', 'ยืนยันการชำระเงินแล้ว');
    }
}
