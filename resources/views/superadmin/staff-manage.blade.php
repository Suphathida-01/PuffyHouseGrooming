<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการช่าง | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/date-picker.css') }}?v={{ filemtime(public_path('css/date-picker.css')) }}">
</head>
<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main">
<div class="staff-page">
    <header class="page-header staff-page-header">
        <div class="page-title">
            <h1>จัดการช่าง</h1>
            <p>{{ auth()->user()->user_role === 'superadmin' ? 'จัดการวันเปิดร้าน กะช่าง และคิวจอง' : 'ดูวันเปิดร้าน กะช่าง และคิวจอง' }}</p>
        </div>
    </header>

    @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
    @if (session('staff_error'))<div class="booking-notice error">{{ session('staff_error') }}</div>@endif
    @if ($errors->any())<div class="booking-notice error">{{ $errors->first() }}</div>@endif

    <div class="staff-content-grid">
    <section class="staff-panel month-panel" aria-labelledby="month-title">
        <div class="month-panel-heading">
            <h2 id="month-title">ตารางร้านประจำเดือน</h2>
            <nav class="month-navigation" aria-label="เปลี่ยนเดือน">
                <a href="{{ route('superadmin.staff.index', ['month' => $previousMonth, 'date' => $previousMonth . '-01']) }}" aria-label="เดือนก่อน">‹</a>
                <strong>{{ \Illuminate\Support\Carbon::parse($selectedMonth . '-01')->locale('th')->translatedFormat('F Y') }}</strong>
                <a href="{{ route('superadmin.staff.index', ['month' => $nextMonth, 'date' => $nextMonth . '-01']) }}" aria-label="เดือนถัดไป">›</a>
            </nav>
            <div class="month-legend" aria-label="สถานะวัน">
                <span><i class="legend-dot legend-open"></i>มีช่างเข้ากะ</span>
                <span><i class="legend-dot legend-unscheduled"></i>ยังไม่จัดกะ</span>
                <span><i class="legend-dot legend-closed"></i>ปิดร้าน</span>
            </div>
        </div>

        <div class="month-weekdays" aria-hidden="true">
            @foreach (['จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.', 'อา.'] as $weekday)
                <span>{{ $weekday }}</span>
            @endforeach
        </div>

        <div class="month-grid">
            @foreach ($calendarDays as $day)
                <article class="month-day {{ $day['is_current_month'] ? '' : 'is-outside-month' }} {{ $day['is_selected'] ? 'is-selected' : '' }} {{ $day['is_today'] ? 'is-today' : '' }} {{ $day['is_closed'] ? 'day-closed' : ($day['working_staff_count'] ? 'day-open' : 'day-unscheduled') }}">
                    <a class="month-day-link" href="{{ route('superadmin.staff.index', ['month' => $selectedMonth, 'date' => $day['date']]) }}">
                        <span class="month-day-number">{{ $day['day'] }}</span>
                        @if ($day['is_closed'])
                            <span class="month-day-state">ปิดร้าน</span>
                        @elseif ($day['working_staff_count'] > 0)
                            <span class="month-day-state">ช่าง {{ $day['working_staff_count'] }} คน</span>
                        @else
                            <span class="month-day-state">ยังไม่จัดกะ</span>
                        @endif
                        @if ($day['booking_count'] > 0)
                            <small>{{ $day['booking_count'] }} คิว</small>
                        @endif
                    </a>
                    @if ($day['is_current_month'] && auth()->user()->user_role === 'superadmin')
                        <form method="POST" action="{{ route('superadmin.staff.closed-days.toggle') }}" class="month-day-toggle">
                            @csrf
                            <input type="hidden" name="work_date" value="{{ $day['date'] }}">
                            <input type="hidden" name="closed" value="{{ $day['is_closed'] ? '0' : '1' }}">
                            <button type="submit" aria-label="{{ $day['is_closed'] ? 'เปิดร้าน' : 'ปิดร้าน' }}วันที่ {{ $day['day'] }}">
                                {{ $day['is_closed'] ? 'เปิดร้าน' : 'ปิดร้าน' }}
                            </button>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="selected-day-section" aria-label="ข้อมูลตารางช่างรายวัน">
        <div class="day-detail-grid">
            <section class="staff-panel day-staff-panel" aria-labelledby="day-staff-title">
                <div class="panel-heading">
                    <div><h3 id="day-staff-title">รายชื่อช่างและกะวันนี้ <small class="day-heading-date">{{ \Illuminate\Support\Carbon::parse($selectedDate)->format('d/m/Y') }}</small></h3><p>{{ auth()->user()->user_role === 'superadmin' ? 'เลือกพนักงานและช่วงเวลาที่เข้าทำงาน' : 'ตรวจสอบรายชื่อและช่วงเวลาทำงานของช่าง' }}</p></div>
                    <div class="staff-actions">
                        @if (auth()->user()->user_role === 'superadmin')
                            <button class="staff-button staff-button-secondary" type="button" data-staff-create-open>+ เพิ่มช่าง</button>
                        @endif
                        <span class="result-count">{{ count($attendanceStaff) }} คน</span>
                    </div>
                </div>
                @if (auth()->user()->user_role === 'superadmin')
                    <form method="POST" action="{{ route('superadmin.staff.attendance.update') }}" class="attendance-form">
                        @csrf
                        <input type="hidden" name="work_date" value="{{ $selectedDate }}">
                @endif
                    <div class="technician-list">
                        @forelse ($attendanceStaff as $staff)
                            <article class="technician-card attendance-row {{ $staff['period'] === 'off' ? 'attendance-is-off' : 'attendance-is-working' }}">
                                <span class="technician-avatar">{{ $staff['initial'] }}</span>
                                <div class="technician-info">
                                    <strong>{{ $staff['name'] }}</strong>
                                    <small>
                                        @if ($staff['period'] === 'custom')
                                            @foreach ($staff['shifts'] as $shift)
                                                {{ substr($shift->start_time, 0, 5) }}–{{ substr($shift->end_time, 0, 5) }}@if (! $loop->last), @endif
                                            @endforeach
                                            · กะเดิม
                                        @elseif ($staff['booking_count'] > 0)
                                            มี {{ $staff['booking_count'] }} คิวในวันนี้
                                        @else
                                            {{ auth()->user()->user_role === 'superadmin' ? 'เลือกกะสำหรับวันนี้' : 'ยังไม่ได้จัดกะ' }}
                                        @endif
                                    </small>
                                </div>
                                @if (auth()->user()->user_role === 'superadmin')
                                    <label class="attendance-period">
                                        <span class="sr-only">กะของ{{ $staff['name'] }}</span>
                                        <select name="attendance[{{ $staff['id'] }}]">
                                            @if ($staff['period'] === 'custom')
                                                <option value="custom" selected>คงกะเดิม</option>
                                            @endif
                                            <option value="off" @selected($staff['period'] === 'off')>ไม่เข้ากะ</option>
                                            <option value="morning" @selected($staff['period'] === 'morning')>ช่วงเช้า · 09:00–13:00</option>
                                            <option value="afternoon" @selected($staff['period'] === 'afternoon')>ช่วงบ่าย · 13:00–17:00</option>
                                            <option value="full_day" @selected($staff['period'] === 'full_day')>ทั้งวัน · 09:00–17:00</option>
                                        </select>
                                    </label>
                                @else
                                    <span class="attendance-period attendance-readonly {{ $staff['period'] === 'off' ? 'is-off' : 'is-working' }}">
                                        @if ($staff['period'] === 'off')
                                            ไม่เข้ากะ
                                        @elseif ($staff['period'] === 'morning')
                                            ช่วงเช้า · 09:00–13:00
                                        @elseif ($staff['period'] === 'afternoon')
                                            ช่วงบ่าย · 13:00–17:00
                                        @elseif ($staff['period'] === 'full_day')
                                            ทั้งวัน · 09:00–17:00
                                        @else
                                            @foreach ($staff['shifts'] as $shift)
                                                {{ substr($shift->start_time, 0, 5) }}–{{ substr($shift->end_time, 0, 5) }}@if (! $loop->last), @endif
                                            @endforeach
                                            · กะเดิม
                                        @endif
                                    </span>
                                @endif
                            </article>
                        @empty
                            @if (! $hasStaffMembers)
                                <p class="staff-empty">ยังไม่มีรายชื่อพนักงาน {{ auth()->user()->user_role === 'superadmin' ? 'กรุณาเพิ่มช่างก่อน' : 'กรุณาติดต่อ SuperAdmin เพื่อเพิ่มช่าง' }}</p>
                            @else
                                <p class="staff-empty">วันนี้ไม่มีช่างเข้ากะ</p>
                            @endif
                        @endforelse
                    </div>
                    @if (count($attendanceStaff) && auth()->user()->user_role === 'superadmin')
                        <div class="attendance-actions"><button class="staff-button staff-button-primary" type="submit">บันทึกกะของวันนี้</button></div>
                    @endif
                @if (auth()->user()->user_role === 'superadmin')
                    </form>
                @endif
            </section>

            <section class="staff-panel day-bookings-panel" aria-labelledby="day-bookings-title">
                <div class="panel-heading">
                    <div><h3 id="day-bookings-title">คิวจองของวันนี้</h3><p>ช่างแสดงตามผู้รับงานในรายการจอง</p></div>
                    <span class="result-count">{{ $dailyBookings->count() }} คิว</span>
                </div>
                <div class="day-bookings-list">
                    @forelse ($dailyBookings as $booking)
                        <article class="day-booking">
                            <time>{{ substr($booking->booking_start_time, 0, 5) }}-{{ substr($booking->booking_end_time, 0, 5) }}</time>
                            <div>
                                <strong>{{ $booking->pet?->pet_name ?? 'ไม่พบข้อมูลสัตว์เลี้ยง' }}</strong>
                                <small>{{ $booking->service?->service_name ?? 'ไม่พบข้อมูลบริการ' }}</small>
                            </div>
                            <span>{{ $booking->admin?->user?->name ?? 'ยังไม่ระบุช่าง' }}</span>
                        </article>
                    @empty
                        <p class="staff-empty">วันนี้ยังไม่มีคิวจอง</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
    </div>
</div>

@if (auth()->user()->user_role === 'superadmin')
<dialog class="schedule-dialog" id="staff-dialog" aria-labelledby="staff-dialog-title">
    <form method="POST" action="{{ route('superadmin.staff.store') }}">
        @csrf
        <div class="schedule-dialog-heading">
            <div><h2 id="staff-dialog-title">เพิ่มช่าง</h2><p>สร้างบัญชีผู้ใช้และเพิ่มในรายชื่อช่าง</p></div>
        </div>
        <label class="schedule-field">ชื่อช่าง<input type="text" name="user_name" maxlength="255" required autocomplete="name"></label>
        <label class="schedule-field">อีเมล<input type="email" name="user_email" maxlength="255" required autocomplete="email"></label>
        <label class="schedule-field">เบอร์โทรศัพท์<input type="tel" name="user_phone" maxlength="20" required autocomplete="tel"></label>
        <label class="schedule-field">รหัสผ่าน<input type="password" name="user_password" minlength="8" required autocomplete="new-password"></label>
        <div class="schedule-dialog-actions">
            <button class="staff-button staff-button-light staff-dialog-close" type="button">ยกเลิก</button>
            <button class="staff-button staff-button-primary" type="submit">เพิ่มช่าง</button>
        </div>
    </form>
</dialog>
@endif

@if (auth()->user()->user_role === 'superadmin')
<script>
    const staffDialog = document.getElementById('staff-dialog');

    document.querySelector('[data-staff-create-open]').addEventListener('click', () => staffDialog.showModal());
    document.querySelectorAll('.staff-dialog-close').forEach((button) => {
        button.addEventListener('click', () => staffDialog.close());
    });

    staffDialog.addEventListener('click', (event) => {
        if (event.target === staffDialog) staffDialog.close();
    });
</script>
@endif
    </main>
</body>
</html>
