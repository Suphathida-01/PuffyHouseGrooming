<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminSchedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminStaffController extends Controller
{
    public function index(Request $request)
    {
        $this->checkAdmin();
        $selectedMonth = $request->query('month', today()->format('Y-m'));
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = today()->format('Y-m');
        }
        $monthStart = Carbon::createFromFormat('!Y-m', $selectedMonth);
        $monthEnd = $monthStart->copy()->endOfMonth();
        $selectedDate = $request->query('date', today()->toDateString());
        $dateParts = explode('-', $selectedDate);
        $dateIsValid = count($dateParts) === 3
            && checkdate((int) $dateParts[1], (int) $dateParts[2], (int) $dateParts[0]);
        if (! $dateIsValid || substr($selectedDate, 0, 7) !== $selectedMonth) {
            $selectedDate = $monthStart->toDateString();
        }

        $staffMembers = Admin::with('user')->whereHas('user')->orderBy('admin_id')->get();

        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $monthSchedules = AdminSchedule::with('admin.user')
            ->whereDate('work_date', '>=', $monthStart->toDateString())
            ->whereDate('work_date', '<=', $monthEnd->toDateString())
            ->get()->groupBy(function ($schedule) {
                return $schedule->work_date->format('Y-m-d');
            });
        $monthBookingRows = Booking::whereDate('booking_date', '>=', $monthStart->toDateString())
            ->whereDate('booking_date', '<=', $monthEnd->toDateString())
            ->whereNull('deleted_at')->get(['booking_id', 'booking_date', 'admin_id']);

        $calendarDays = [];
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $dateKey = $day->toDateString();
            $daySchedules = $monthSchedules->get($dateKey, collect());
            $availableStaffCount = $daySchedules->where('status', 'available')->pluck('admin_id')->unique()->count();
            $closed = $daySchedules->count() > 0
                && $availableStaffCount === 0
                && $daySchedules->where('status', 'unavailable')->count() > 0;
            $bookingCount = $monthBookingRows->filter(function ($booking) use ($dateKey) {
                return Carbon::parse($booking->booking_date)->format('Y-m-d') === $dateKey;
            })->count();

            $calendarDays[] = [
                'date' => $dateKey,
                'day' => $day->day,
                'is_current_month' => $day->month === $monthStart->month,
                'is_selected' => $dateKey === $selectedDate,
                'is_today' => $dateKey === today()->toDateString(),
                'is_closed' => $closed,
                'working_staff_count' => $availableStaffCount,
                'booking_count' => $bookingCount,
            ];
        }

        $dailySchedules = AdminSchedule::with('admin.user')
            ->whereDate('work_date', $selectedDate)
            ->orderBy('start_time')->get();
        $dailyBookings = Booking::with(['pet', 'service', 'admin.user'])
            ->whereDate('booking_date', $selectedDate)
            ->whereNull('deleted_at')
            ->orderBy('booking_start_time')->get();
        $attendanceStaff = [];
        foreach ($staffMembers as $staffMember) {
            $name = $staffMember->user->name;
            $shifts = $dailySchedules->where('admin_id', $staffMember->admin_id)
                ->where('status', 'available')->values();
            $startTime = $shifts->min('start_time');
            $endTime = $shifts->max('end_time');

            $period = match (true) {
                $shifts->isEmpty() => 'off',
                $startTime <= '09:00:00' && $endTime >= '17:00:00' => 'full_day',
                $startTime === '09:00:00' && $endTime === '13:00:00' => 'morning',
                $startTime === '13:00:00' && $endTime === '17:00:00' => 'afternoon',
                default => 'custom',
            };

            $attendanceStaff[] = [
                'id' => $staffMember->admin_id,
                'name' => $name,
                'initial' => mb_substr($name, 0, 1),
                'period' => $period,
                'shifts' => $shifts,
                'booking_count' => $dailyBookings->where('admin_id', $staffMember->admin_id)->count(),
            ];
        }

        $hasStaffMembers = $staffMembers->isNotEmpty();
        if (Auth::user()->user_role === 'admin') {
            $attendanceStaff = array_values(array_filter($attendanceStaff, fn ($staff) => $staff['period'] !== 'off'));
        }

        return view('staff-manage', [
            'calendarDays' => $calendarDays,
            'selectedMonth' => $selectedMonth,
            'previousMonth' => $monthStart->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->copy()->addMonth()->format('Y-m'),
            'selectedDate' => $selectedDate,
            'attendanceStaff' => $attendanceStaff,
            'hasStaffMembers' => $hasStaffMembers,
            'dailyBookings' => $dailyBookings,
        ]);
    }

    private function checkAdmin(): void
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);
    }
}
