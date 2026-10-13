<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);

        $reportYear = now()->year;
        $displayYear = $reportYear;
        $totalCustomers = Customer::count();
        $totalPets = Pet::count();
        $totalBookings = Booking::whereYear('booking_date', $reportYear)->count();

        $statusCounts = Booking::whereYear('booking_date', $reportYear)
            ->get(['booking_status'])
            ->countBy('booking_status');
        $statusDefinitions = [
            ['key' => 'pending', 'label' => 'รอดำเนินการ', 'color' => 'orange'],
            ['key' => 'cancelled', 'label' => 'ยกเลิก', 'color' => 'red'],
            ['key' => 'completed', 'label' => 'เสร็จสิ้น', 'color' => 'green'],
        ];
        $bookingStatusSummary = [];
        $statusTotal = max($totalBookings, 1);
        foreach ($statusDefinitions as $status) {
            $status['total'] = (int) ($statusCounts->get($status['key'], 0));
            $status['percent'] = (int) round(($status['total'] / $statusTotal) * 100);
            $bookingStatusSummary[] = $status;
        }

        $popularServiceBookings = fn ($query) => $query
            ->where('booking_status', '!=', 'cancelled')
            ->whereYear('booking_date', $reportYear);
        $popularServices = Service::query()
            ->withCount(['bookings as bookings_count' => $popularServiceBookings])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        return view('admin-dashboard', compact('totalCustomers', 'totalPets', 'totalBookings', 'bookingStatusSummary', 'popularServices', 'displayYear'));
    }
}