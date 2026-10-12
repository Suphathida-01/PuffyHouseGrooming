<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperadminDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->user_role === 'superadmin', 403);

        $totalCustomers = Customer::count();
        $totalPets = Pet::count();

        $currentYear = (int) date('Y');
        $selectedYear = (int) $request->query('year', $currentYear);
        if ($selectedYear < 2000 || $selectedYear > $currentYear) {
            $selectedYear = $currentYear;
        }
        $totalBookings = Booking::whereYear('booking_date', $selectedYear)->count();
        $completedPayments = Payment::where('payment_status', 'completed')
            ->whereYear('payment_date', $selectedYear);
        $totalRevenue = (float) (clone $completedPayments)->sum('payment_amount');

        $monthNames = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $monthlyRevenue = [];
        $revenueByMonth = [];
        $payments = (clone $completedPayments)->get(['payment_amount', 'payment_date']);

        foreach ($payments as $payment) {
            $monthKey = $payment->payment_date->format('Y-m');
            if (! isset($revenueByMonth[$monthKey])) {
                $revenueByMonth[$monthKey] = 0;
            }
            $revenueByMonth[$monthKey] += (float) $payment->payment_amount;
        }

        $maxMonthlyRevenue = 1;
        for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {
            $monthKey = sprintf('%04d-%02d', $selectedYear, $monthNumber);
            $monthTotal = isset($revenueByMonth[$monthKey]) ? $revenueByMonth[$monthKey] : 0;

            if ($monthTotal > $maxMonthlyRevenue) {
                $maxMonthlyRevenue = $monthTotal;
            }

            $monthlyRevenue[] = [
                'key' => $monthKey,
                'label' => $monthNames[$monthNumber],
                'total' => $monthTotal,
            ];
        }

        foreach ($monthlyRevenue as $index => $month) {
            $height = $month['total'] > 0 ? round(($month['total'] / $maxMonthlyRevenue) * 100) : 3;
            if ($height > 0 && $height < 8) {
                $height = 8;
            }
            $monthlyRevenue[$index]['height'] = $height;
        }

        $statusCounts = Booking::whereYear('booking_date', $selectedYear)
            ->get(['booking_status'])
            ->countBy('booking_status')
            ->all();

        $statusDefinitions = [
            ['key' => 'pending', 'label' => 'รอดำเนินการ', 'color' => 'orange'],
            ['key' => 'cancelled', 'label' => 'ยกเลิก', 'color' => 'red'],
            ['key' => 'completed', 'label' => 'เสร็จสิ้น', 'color' => 'green'],
        ];
        $bookingStatusSummary = [];
        $statusTotal = $totalBookings > 0 ? $totalBookings : 1;
        foreach ($statusDefinitions as $status) {
            $status['total'] = isset($statusCounts[$status['key']]) ? $statusCounts[$status['key']] : 0;
            $status['percent'] = round(($status['total'] / $statusTotal) * 100);
            $bookingStatusSummary[] = $status;
        }

        $popularServiceBookings = fn ($query) => $query
            ->where('booking_status', '!=', 'cancelled')
            ->whereYear('booking_date', $selectedYear);
        $popularServices = Service::query()
            ->withCount(['bookings as bookings_count' => $popularServiceBookings])
            ->withSum(['bookings as bookings_sum_booking_total_price' => $popularServiceBookings], 'booking_total_price')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();
        $dayNames = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        $monthNamesLong = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        $displayDate = 'วัน'.$dayNames[(int) date('w')].'ที่ '.date('j').' '.$monthNamesLong[(int) date('n')].' '.(date('Y') + 543);

        return view('dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalPets' => $totalPets,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'selectedYear' => $selectedYear,
            'maxYear' => $currentYear,
            'bookingStatusSummary' => $bookingStatusSummary,
            'popularServices' => $popularServices,
            'displayDate' => $displayDate,
        ]);
    }
}