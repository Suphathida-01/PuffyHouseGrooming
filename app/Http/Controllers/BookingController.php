<?php

namespace App\Http\Controllers;

use App\Models\AdminSchedule;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function myBookings(Request $request)
    {
        $customer = $this->customer();
        $filter = $request->query('filter', 'all');
        if (! in_array($filter, ['all', 'upcoming', 'payment_pending', 'completed', 'cancelled'], true)) {
            $filter = 'all';
        }

        $allBookings = $customer->bookings()
            ->with(['pet.breed.petType', 'service', 'payments'])
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_start_time')
            ->get();

        $upcomingStatuses = ['pending'];
        $upcomingCount = $allBookings->filter(fn ($booking) =>
            in_array($booking->booking_status, $upcomingStatuses, true)
            && $booking->booking_date->greaterThanOrEqualTo(today())
        )->count();
        $completedCount = $allBookings->where('booking_status', 'completed')->count();
        $bookings = match ($filter) {
            'payment_pending' => $allBookings->filter(fn ($booking) =>
                $booking->payments->sortByDesc('payment_id')->first()?->payment_status === 'pending'
            )->values(),
            'upcoming' => $allBookings->filter(fn ($booking) =>
                in_array($booking->booking_status, $upcomingStatuses, true)
                && $booking->booking_date->greaterThanOrEqualTo(today())
            )->values(),
            'completed' => $allBookings->where('booking_status', 'completed')->values(),
            'cancelled' => $allBookings->where('booking_status', 'cancelled')->values(),
            default => $allBookings,
        };

        return view('booking.index', compact('bookings', 'allBookings', 'upcomingCount', 'completedCount', 'filter'));
    }

    public function cancelMyBooking($bookingId)
    {
        $booking = $this->customer()->bookings()->with('payments')->findOrFail($bookingId);

        if ($booking->booking_status !== 'pending') {
            return back()->with('booking_error', 'รายการนี้ไม่สามารถยกเลิกได้');
        }

        $cancellationDeadline = $booking->booking_date->copy()->subDay()->endOfDay();
        if (now()->greaterThan($cancellationDeadline)) {
            return back()->with('booking_error', 'ยกเลิกได้ถึงวันก่อนวันนัดเท่านั้น');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['booking_status' => 'cancelled']);
            $booking->payments()->where('payment_status', 'pending')->update(['payment_status' => 'failed']);
        });

        $message = $booking->payments->contains('payment_status', 'completed')
            ? 'ยกเลิกรายการจองแล้ว หากชำระเงินแล้ว กรุณาติดต่อร้านเพื่อดำเนินการเรื่องคืนเงิน'
            : 'ยกเลิกรายการจองแล้ว';

        return redirect()->route('my-bookings_index', ['filter' => 'cancelled'])
            ->with('success', $message);
    }

    public function show($serviceId)
    {
        $customer = $this->customer();
        $service = Service::where('service_status', 'active')->findOrFail($serviceId);
        $pets = $customer->pets()->with('breed.petType')->get();
        $selectedPet = request()->filled('pet_id') ? $pets->firstWhere('id', request('pet_id')) : null;
        $pet = $selectedPet;
        $price = $pet ? $this->priceFor($service, $pet) : 0;

        return view('booking.pets', compact('service', 'pets', 'selectedPet', 'pet', 'price'));
    }

    public function choosePet(Request $request, $serviceId)
    {
        $customer = $this->customer();
        $request->validate(['pet_id' => ['required', 'exists:pets,id']]);
        $pet = $customer->pets()->findOrFail($request->pet_id);
        $service = Service::where('service_status', 'active')->findOrFail($serviceId);

        session(['booking' => [
            'service_id' => $service->service_id,
            'pet_id' => $pet->id,
            'price' => $this->priceFor($service, $pet),
        ]]);

        return redirect()->route('booking_schedule', $serviceId);
    }

    public function schedule(Request $request, $serviceId)
    {
        [$service, $pet] = $this->bookingDetails($serviceId);
        $price = $this->priceFor($service, $pet);
        $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $month = Carbon::createFromFormat('!Y-m', $request->query('month', today()->format('Y-m')));
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $availableFrom = $monthStart->lt(today()->startOfMonth()) ? today() : $monthStart;
        $latestBookableDate = today()->addDays(6);
        $availableUntil = $monthEnd->lt($latestBookableDate) ? $monthEnd : $latestBookableDate;
        $duration = max(1, (int) $service->service_duration_minutes);
        $shifts = AdminSchedule::where('status', 'available')
            ->whereDate('work_date', '>=', $availableFrom)
            ->whereDate('work_date', '<=', $availableUntil)
            ->orderBy('work_date')->orderBy('start_time')->get();
        $bookings = Booking::where('booking_status', 'pending')
            ->whereDate('booking_date', '>=', $availableFrom)
            ->whereDate('booking_date', '<=', $monthEnd)->get();
        $slotCandidates = [];

        foreach ($shifts as $shift) {
            if ($shift->work_date->isSaturday()) {
                continue;
            }

            $shiftStart = Carbon::parse($shift->work_date->format('Y-m-d').' '.$shift->start_time);
            $shiftEnd = Carbon::parse($shift->work_date->format('Y-m-d').' '.$shift->end_time);

            for ($start = $shiftStart->copy(); $start->copy()->addMinutes($duration)->lte($shiftEnd); $start->addHour()) {
                if ($start->lt(now())) {
                    continue;
                }

                $end = $start->copy()->addMinutes($duration);
                $dateKey = $shift->work_date->format('Y-m-d');
                $key = $dateKey.' '.$start->format('H:i');
                $slotCandidates[$key] ??= [
                    'work_date' => $shift->work_date,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'admin_ids' => [],
                ];
                $slotCandidates[$key]['admin_ids'][] = (int) $shift->admin_id;
            }
        }

        $schedules = [];
        foreach ($slotCandidates as $key => $slot) {
            $candidateIds = array_values(array_unique($slot['admin_ids']));
            $busyIds = $bookings->filter(function ($booking) use ($slot, $candidateIds) {
                return in_array((int) $booking->admin_id, $candidateIds, true)
                    && $booking->booking_date->format('Y-m-d') === $slot['work_date']->format('Y-m-d')
                    && substr($booking->booking_start_time, 0, 5) < $slot['end_time']
                    && substr($booking->booking_end_time, 0, 5) > $slot['start_time'];
            })->pluck('admin_id')->map(fn ($id) => (int) $id)->unique()->all();

            $slot['available_staff_count'] = count(array_diff($candidateIds, $busyIds));
            unset($slot['admin_ids']);
            $schedules[$key] = $slot;
        }

        $availableByDate = collect($schedules)->groupBy(fn ($slot) => $slot['work_date']->format('Y-m-d'));
        $selectedDate = $request->query('date');
        if (! $selectedDate || ! $availableByDate->has($selectedDate)) {
            $selectedDate = null;
        }
        $daySchedules = $selectedDate ? $availableByDate->get($selectedDate, collect())->values()->all() : [];
        $hasAvailableSlot = collect($daySchedules)->contains(fn ($slot) => $slot['available_staff_count'] > 0);
        $selectedDateLabel = $selectedDate ? Carbon::parse($selectedDate)->format('d/m/Y') : null;

        $calendarCells = array_fill(0, $monthStart->dayOfWeekIso - 1, null);
        for ($day = 1; $day <= $monthStart->daysInMonth; $day++) {
            $date = $monthStart->copy()->day($day);
            $dateKey = $date->format('Y-m-d');
            $calendarCells[] = [
                'day' => $day,
                'date' => $dateKey,
                'available' => $availableByDate->has($dateKey),
                'selected' => $selectedDate === $dateKey,
            ];
        }
        while (count($calendarCells) % 7 !== 0) {
            $calendarCells[] = null;
        }
        $calendarRows = array_chunk($calendarCells, 7);
        $thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        $monthTitle = $thaiMonths[$month->month].' '.($month->year + 543);
        $monthKey = $month->format('Y-m');
        $previousMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        return view('booking.schedule', compact('service', 'pet', 'schedules', 'daySchedules', 'selectedDate', 'selectedDateLabel', 'hasAvailableSlot', 'calendarRows', 'monthTitle', 'monthKey', 'previousMonth', 'nextMonth', 'price'));
    }

    public function chooseSchedule(Request $request, $serviceId)
    {
        [$service, $pet] = $this->bookingDetails($serviceId);
        $data = $request->validate(['slot' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}\|\d{2}:\d{2}$/']]);
        [$workDate, $startTime] = explode('|', $data['slot'], 2);
        [$year, $month, $day] = array_map('intval', explode('-', $workDate));
        [$hour, $minute] = array_map('intval', explode(':', $startTime));
        abort_unless(checkdate($month, $day, $year) && $hour < 24 && $minute < 60, 422);

        $start = Carbon::parse($workDate.' '.$startTime);
        $end = $start->copy()->addMinutes((int) $service->service_duration_minutes);
        abort_if(Carbon::parse($workDate)->isSaturday() || Carbon::parse($workDate)->gt(today()->addDays(6)) || $start->lt(now()), 409);

        if (empty($this->availableStaffForSlot($workDate, $start->format('H:i:s'), $end->format('H:i:s')))) {
            return redirect()->route('booking_schedule', ['serviceId' => $serviceId, 'month' => substr($workDate, 0, 7), 'date' => $workDate])
                ->with('booking_error', 'ช่วงเวลานี้มีผู้จองครบทุกช่างแล้ว กรุณาเลือกเวลาอื่น');
        }

        $bookingSession = session('booking');
        $bookingSession['work_date'] = $workDate;
        $bookingSession['start_time'] = $start->format('H:i:s');
        $bookingSession['end_time'] = $end->format('H:i:s');
        session(['booking' => $bookingSession]);

        return redirect()->route('booking_confirm', $serviceId);
    }

    public function confirm($serviceId)
    {
        [$service, $pet] = $this->bookingDetails($serviceId);
        $price = $this->priceFor($service, $pet);
        $workDate = session('booking.work_date');
        $bookingStart = session('booking.start_time');
        $bookingEnd = session('booking.end_time');
        abort_unless($workDate && $bookingStart && $bookingEnd, 404);

        if (empty($this->availableStaffForSlot($workDate, $bookingStart, $bookingEnd))) {
            return redirect()->route('booking_schedule', ['serviceId' => $serviceId, 'month' => substr($workDate, 0, 7), 'date' => $workDate])
                ->with('booking_error', 'ช่วงเวลานี้เพิ่งถูกจองครบแล้ว กรุณาเลือกเวลาอื่น');
        }

        return view('booking.confirm', compact('service', 'pet', 'workDate', 'bookingStart', 'bookingEnd', 'price'));
    }

    public function store(Request $request, $serviceId)
    {
        $customer = $this->customer();
        [$service, $pet] = $this->bookingDetails($serviceId);
        $workDate = session('booking.work_date');
        $bookingStart = session('booking.start_time');
        $bookingEnd = session('booking.end_time');
        abort_unless($workDate && $bookingStart && $bookingEnd, 404);
        $data = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'booking_notes' => ['nullable', 'string', 'max:1000'],
            'accept_terms' => ['accepted'],
        ]);
        $booking = DB::transaction(function () use ($customer, $service, $pet, $data, $workDate, $bookingStart, $bookingEnd) {
            $availableStaffIds = $this->availableStaffForSlot($workDate, $bookingStart, $bookingEnd, true);
            if (empty($availableStaffIds)) {
                return null;
            }

            $adminId = $availableStaffIds[array_rand($availableStaffIds)];
            auth()->user()->update(['name' => $data['contact_name'], 'user_phone' => $data['contact_phone']]);

            return Booking::create([
                'booking_date' => $workDate,
                'booking_start_time' => $bookingStart,
                'booking_end_time' => $bookingEnd,
                'booking_total_price' => $this->priceFor($service, $pet),
                'booking_status' => 'pending',
                'booking_notes' => $data['booking_notes'] ?? null,
                'customer_id' => $customer->customer_id,
                'admin_id' => $adminId,
                'service_id' => $service->service_id,
                'pet_id' => $pet->id,
            ]);
        });

        if (! $booking) {
            return redirect()->route('booking_schedule', ['serviceId' => $serviceId, 'month' => substr($workDate, 0, 7), 'date' => $workDate])
                ->with('booking_error', 'ช่วงเวลานี้เพิ่งถูกจองครบแล้ว กรุณาเลือกเวลาอื่น');
        }

        session()->forget('booking');

        return redirect()->route('payments_create', $booking->booking_id);
    }

    private function customer(): Customer
    {
        abort_unless(auth()->user()->user_role === 'customer', 403);

        return Customer::firstOrCreate(['user_id' => auth()->id()]);
    }

    private function bookingDetails($serviceId): array
    {
        $data = session('booking');
        abort_unless($data && (int) $data['service_id'] === (int) $serviceId, 404);
        $service = Service::where('service_status', 'active')->findOrFail($serviceId);
        $pet = $this->customer()->pets()->with('breed.petType')->findOrFail($data['pet_id']);
        return [$service, $pet];
    }

    private function availableStaffForSlot(string $workDate, string $startTime, string $endTime, bool $lock = false): array
    {
        $scheduleQuery = AdminSchedule::where('status', 'available')
            ->whereDate('work_date', $workDate)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->orderBy('admin_id');

        if ($lock) {
            $scheduleQuery->lockForUpdate();
        }

        $staffIds = $scheduleQuery->pluck('admin_id')->map(fn ($id) => (int) $id)->unique()->values()->all();
        if (empty($staffIds)) {
            return [];
        }

        $bookingQuery = Booking::whereDate('booking_date', $workDate)
            ->whereIn('admin_id', $staffIds)
            ->where('booking_status', 'pending')
            ->where('booking_start_time', '<', $endTime)
            ->where('booking_end_time', '>', $startTime);

        if ($lock) {
            $bookingQuery->lockForUpdate();
        }

        $busyIds = $bookingQuery->pluck('admin_id')->map(fn ($id) => (int) $id)->unique()->all();

        return array_values(array_diff($staffIds, $busyIds));
    }

    private function priceFor(Service $service, Pet $pet): float
    {
        $column = $pet->pet_weight <= 5 ? 'price_small' : ($pet->pet_weight <= 15 ? 'price_medium' : 'price_large');

        return (float) $service->{$column};
    }
}