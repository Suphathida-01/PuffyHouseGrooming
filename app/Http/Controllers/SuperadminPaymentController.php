<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperadminPaymentController extends Controller
{
    public function show($paymentId)
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);

        $payment = Payment::findOrFail($paymentId);

        return redirect()->route('superadmin.payments', [
            'date' => $payment->payment_date->toDateString(),
            'detail' => $payment->payment_id,
        ]);
    }

    public function index(Request $request)
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);

        $selectedDate = $request->query('date', '');
        if (! is_string($selectedDate)) {
            $selectedDate = '';
        }
        if ($selectedDate !== '') {
            $dateParts = explode('-', $selectedDate);
            $dateIsValid = count($dateParts) === 3
                && checkdate((int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[0]);
            if (! $dateIsValid) {
                $selectedDate = '';
            }
        }

        $selectedStatus = $request->query('status', 'all');
        if (! in_array($selectedStatus, ['all', 'completed', 'pending', 'failed'], true)) {
            $selectedStatus = 'all';
        }

        // Summary cards and the table use the same selected payment date;
        // with no date they show the full payment history.
        $completedPayments = Payment::where('payment_status', 'completed');
        $failedPayments = Payment::where('payment_status', 'failed');
        $pendingPayments = Payment::where('payment_status', 'pending');
        if ($selectedDate !== '') {
            $completedPayments->whereDate('payment_date', $selectedDate);
            $failedPayments->whereDate('payment_date', $selectedDate);
            $pendingPayments->whereDate('payment_date', $selectedDate);
        }
        $summary = [
            'completedRevenue' => (float) (clone $completedPayments)->sum('payment_amount'),
            'completedCount' => (clone $completedPayments)->count(),
            'pendingCount' => (clone $pendingPayments)->count(),
            'pendingTotal' => (float) (clone $pendingPayments)->sum('payment_amount'),
            'failedCount' => (clone $failedPayments)->count(),
            'failedTotal' => (float) (clone $failedPayments)->sum('payment_amount'),
        ];

        $paymentsQuery = Payment::with(['booking.customer.user', 'booking.service'])
            ->orderByDesc('payment_date')->orderByDesc('payment_id');
        if ($selectedDate !== '') {
            $paymentsQuery->whereDate('payment_date', $selectedDate);
        }
        if ($selectedStatus !== 'all') {
            $paymentsQuery->where('payment_status', $selectedStatus);
        }
        $payments = $paymentsQuery->get();
        $selectedPaymentId = filter_var($request->query('detail'), FILTER_VALIDATE_INT);
        $selectedPayment = $selectedPaymentId
            ? Payment::with(['booking.customer.user', 'booking.service', 'booking.pet.breed.petType'])
                ->findOrFail($selectedPaymentId)
            : null;

        return view('superadmin.payments-manage', compact('payments', 'summary', 'selectedDate', 'selectedStatus', 'selectedPayment'));
    }

    public function confirm($paymentId)
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);

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

        return redirect()->route('superadmin.payments', [
            'date' => today()->toDateString(),
            'status' => 'completed',
        ])->with('success', 'ยืนยันการชำระเงินแล้ว');
    }

    public function reject($paymentId)
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);

        $rejected = DB::transaction(function () use ($paymentId) {
            $payment = Payment::lockForUpdate()->findOrFail($paymentId);
            if ($payment->payment_status !== 'pending') {
                return false;
            }

            $payment->update([
                'payment_status' => 'failed',
                'payment_date' => now(),
            ]);

            return true;
        });

        if (! $rejected) {
            return back()->with('payment_error', 'รายการนี้ไม่ได้อยู่ในสถานะรอชำระแล้ว');
        }

        return redirect()->route('superadmin.payments', [
            'date' => today()->toDateString(),
            'status' => 'failed',
        ])->with('success', 'ปฏิเสธรายการชำระเงินแล้ว');
    }
}
