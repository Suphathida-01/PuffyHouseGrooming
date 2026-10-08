<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminSchedule;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        return view('staff-manage', [
            'calendarDays' => $calendarDays,
            'selectedMonth' => $selectedMonth,
            'previousMonth' => $monthStart->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->copy()->addMonth()->format('Y-m'),
            'selectedDate' => $selectedDate,
            'attendanceStaff' => $attendanceStaff,
            'dailyBookings' => $dailyBookings,
        ]);
    }

    public function saveAttendance(Request $request)
    {
        $this->checkAdmin();
        $data = $request->validate([
            'work_date' => ['required', 'date_format:Y-m-d'],
            'attendance' => ['nullable', 'array'],
            'attendance.*' => ['required', Rule::in(['off', 'morning', 'afternoon', 'full_day', 'custom'])],
        ]);

        $staffMembers = Admin::whereHas('user')->orderBy('admin_id')->get();
        $periodTimes = [
            'morning' => ['09:00:00', '13:00:00'],
            'afternoon' => ['13:00:00', '17:00:00'],
            'full_day' => ['09:00:00', '17:00:00'],
        ];
        $attendance = $data['attendance'] ?? [];
        $changes = [];

        foreach ($staffMembers as $staffMember) {
            $period = $attendance[$staffMember->admin_id] ?? 'off';
            if ($period === 'custom') {
                continue;
            }

            $existingSchedules = AdminSchedule::where('admin_id', $staffMember->admin_id)
                ->whereDate('work_date', $data['work_date'])->get();
            $existingAvailable = $existingSchedules->where('status', 'available')
                ->map(fn ($schedule) => [substr($schedule->start_time, 0, 8), substr($schedule->end_time, 0, 8)])
                ->sort()->values()->all();
            $desired = isset($periodTimes[$period]) ? [$periodTimes[$period]] : [];

            if ($existingAvailable === $desired) {
                continue;
            }

            $hasBookings = Booking::where('admin_id', $staffMember->admin_id)
                ->whereDate('booking_date', $data['work_date'])
                ->whereNull('deleted_at')
                ->whereIn('booking_status', ['pending', 'confirmed', 'completed'])
                ->exists();

            if ($hasBookings) {
                return back()->withInput()->with('staff_error', 'ไม่สามารถเปลี่ยนกะของ'.$staffMember->user->name.'ได้ เพราะมีคิวจองในวันที่เลือก');
            }

            $changes[] = [
                'admin_id' => $staffMember->admin_id,
                'period' => $period,
                'times' => $periodTimes[$period] ?? null,
            ];
        }

        DB::transaction(function () use ($changes, $data) {
            foreach ($changes as $change) {
                $schedules = AdminSchedule::where('admin_id', $change['admin_id'])
                    ->whereDate('work_date', $data['work_date']);

                if ($change['period'] === 'off') {
                    $schedules->where('status', 'available')->delete();
                    continue;
                }

                $schedules->delete();
                AdminSchedule::create([
                    'admin_id' => $change['admin_id'],
                    'work_date' => $data['work_date'],
                    'start_time' => $change['times'][0],
                    'end_time' => $change['times'][1],
                    'status' => 'available',
                ]);
            }
        });

        return redirect()->route('staff.index', [
            'month' => substr($data['work_date'], 0, 7),
            'date' => $data['work_date'],
        ])->with('success', 'บันทึกกะของพนักงานในวันที่เลือกแล้ว');
    }

    public function storeStaff(Request $request)
    {
        $this->checkAdmin();
        $data = $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'user_phone' => ['required', 'string', 'max:20'],
            'user_password' => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'user_phone' => $data['user_phone'],
                'password' => $data['user_password'],
                'user_role' => 'admin',
            ]);
            Admin::create(['user_id' => $user->id]);
        });

        return back()->with('success', 'เพิ่มช่างแล้ว');
    }

    public function storeSchedule(Request $request)
    {
        $this->checkAdmin();
        $data = $request->validate([
            'admin_id' => ['required', 'exists:admins,admin_id'],
            'work_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'in:available,unavailable'],
        ]);
        $overlap = AdminSchedule::where('admin_id', $data['admin_id'])
            ->whereDate('work_date', $data['work_date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])->exists();

        if ($overlap) {
            return back()->withInput()->with('staff_error', 'เวลานี้ทับซ้อนกับตารางงานเดิมของช่าง');
        }

        AdminSchedule::create($data);

        return back()->with('success', 'บันทึกตารางงานแล้ว');
    }

    public function toggleClosedDay(Request $request)
    {
        $this->checkAdmin();
        $data = $request->validate([
            'work_date' => ['required', 'date_format:Y-m-d'],
            'closed' => ['required', 'in:0,1'],
        ]);
        $staffMembers = Admin::whereHas('user')->get();
        if ($staffMembers->isEmpty()) {
            return back()->with('staff_error', 'กรุณาเพิ่มช่างก่อนกำหนดวันปิดร้าน');
        }

        if ($data['closed'] === '1') {
            foreach ($staffMembers as $staffMember) {
                $existingSchedules = AdminSchedule::where('admin_id', $staffMember->admin_id)
                    ->whereDate('work_date', $data['work_date'])->get();
                if ($existingSchedules->isEmpty()) {
                    AdminSchedule::create([
                        'admin_id' => $staffMember->admin_id,
                        'work_date' => $data['work_date'],
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'status' => 'unavailable',
                    ]);
                } else {
                    AdminSchedule::where('admin_id', $staffMember->admin_id)
                        ->whereDate('work_date', $data['work_date'])->update(['status' => 'unavailable']);
                }
            }
            $message = 'ปิดร้านในวันที่เลือกแล้ว';
        } else {
            AdminSchedule::whereDate('work_date', $data['work_date'])
                ->where('status', 'unavailable')->update(['status' => 'available']);
            $message = 'เปิดร้านในวันที่เลือกแล้ว';
        }

        return back()->with('success', $message);
    }

    private function checkAdmin(): void
    {
        abort_unless(Auth::user()->user_role === 'admin', 403);
    }
}
