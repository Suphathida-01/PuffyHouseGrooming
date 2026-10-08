<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->user_role !== 'admin') {
            return redirect()->route('home');
        }

        $result = DB::select('SELECT COUNT(*) AS total FROM customers WHERE deleted_at IS NULL');
        $totalCustomers = (int) $result[0]->total;

        $result = DB::select('SELECT COUNT(*) AS total FROM pets WHERE deleted_at IS NULL');
        $totalPets = (int) $result[0]->total;

        $result = DB::select('SELECT COUNT(*) AS total FROM bookings WHERE deleted_at IS NULL');
        $totalBookings = (int) $result[0]->total;

        $result = DB::select('SELECT SUM(payment_amount) AS total FROM payments WHERE payment_status = ? AND deleted_at IS NULL', ['completed']);
        $totalRevenue = (float) ($result[0]->total ?? 0);

        $monthNames = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $maxMonth = date('Y-m');
        $selectedMonth = $request->query('month', $maxMonth);
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $selectedMonth) || $selectedMonth > $maxMonth) {
            $selectedMonth = $maxMonth;
        }
        $selectedMonthStart = strtotime($selectedMonth.'-01');
        $monthlyRevenue = [];
        $revenueByMonth = [];
        $firstMonth = date('Y-m-01 00:00:00', strtotime('-5 months', $selectedMonthStart));
        $lastMonth = date('Y-m-t 23:59:59', $selectedMonthStart);
        $payments = DB::select(
            'SELECT payment_amount, payment_date FROM payments WHERE payment_status = ? AND payment_date >= ? AND payment_date <= ? AND deleted_at IS NULL',
            ['completed', $firstMonth, $lastMonth],
        );

        foreach ($payments as $payment) {
            $monthKey = date('Y-m', strtotime($payment->payment_date));
            if (! isset($revenueByMonth[$monthKey])) {
                $revenueByMonth[$monthKey] = 0;
            }
            $revenueByMonth[$monthKey] += (float) $payment->payment_amount;
        }

        $maxMonthlyRevenue = 1;
        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $monthDate = strtotime('-'.$monthsAgo.' months', $selectedMonthStart);
            $monthKey = date('Y-m', $monthDate);
            $monthNumber = (int) date('n', $monthDate);
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

        $statusRows = DB::select('SELECT booking_status, COUNT(*) AS total FROM bookings WHERE deleted_at IS NULL GROUP BY booking_status');
        $statusCounts = [];
        foreach ($statusRows as $statusRow) {
            $statusCounts[$statusRow->booking_status] = (int) $statusRow->total;
        }

        $statusDefinitions = [
            ['key' => 'completed', 'label' => 'เสร็จสิ้น', 'color' => 'green'],
            ['key' => 'confirmed', 'label' => 'ยืนยันแล้ว', 'color' => 'blue'],
            ['key' => 'pending', 'label' => 'รอดำเนินการ', 'color' => 'orange'],
            ['key' => 'cancelled', 'label' => 'ยกเลิก', 'color' => 'red'],
        ];
        $bookingStatusSummary = [];
        $statusTotal = $totalBookings > 0 ? $totalBookings : 1;
        foreach ($statusDefinitions as $status) {
            $status['total'] = isset($statusCounts[$status['key']]) ? $statusCounts[$status['key']] : 0;
            $status['percent'] = round(($status['total'] / $statusTotal) * 100);
            $bookingStatusSummary[] = $status;
        }

        $popularServices = DB::select(
            "SELECT services.service_name, COUNT(bookings.booking_id) AS bookings_count,
                COALESCE(SUM(bookings.booking_total_price), 0) AS bookings_sum_booking_total_price
            FROM services
            LEFT JOIN bookings ON bookings.service_id = services.service_id
                AND bookings.deleted_at IS NULL
                AND bookings.booking_status != 'cancelled'
                AND (
                    bookings.booking_status = 'completed'
                    OR EXISTS (
                        SELECT 1 FROM payments
                        WHERE payments.booking_id = bookings.booking_id
                            AND payments.payment_status = 'completed'
                            AND payments.deleted_at IS NULL
                    )
                )
            WHERE services.deleted_at IS NULL
            GROUP BY services.service_id, services.service_name
            ORDER BY bookings_count DESC
            LIMIT 5",
        );
        $dayNames = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        $monthNamesLong = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        $displayDate = 'วัน'.$dayNames[(int) date('w')].'ที่ '.date('j').' '.$monthNamesLong[(int) date('n')].' '.(date('Y') + 543);

        return view('dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalPets' => $totalPets,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'selectedMonth' => $selectedMonth,
            'maxMonth' => $maxMonth,
            'bookingStatusSummary' => $bookingStatusSummary,
            'popularServices' => $popularServices,
            'displayDate' => $displayDate,
        ]);
    }
}
