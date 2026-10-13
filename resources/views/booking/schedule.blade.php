<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เลือกวันเวลา | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/date-picker.css') }}?v={{ filemtime(public_path('css/date-picker.css')) }}">
</head>

<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon"
                src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly
            House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>
    <main class="booking-page">
        <div class="booking-steps"><span class="done"><b>✓</b> สัตว์เลี้ยง</span><i></i><span class="active"><b>2</b>
                วันเวลา</span><i></i><span><b>3</b> ยืนยันข้อมูล</span><i></i><span><b>4</b> ชำระเงิน</span></div>
        <div class="booking-layout">
            <section class="booking-main">
                <div class="booking-panel">
                    <div class="booking-calendar-heading">
                        <h1>เลือกวันที่ต้องการจอง 📅</h1>
                        <div class="booking-month-nav">
                            <a href="{{ route('booking_schedule', ['serviceId' => $service->service_id, 'month' => $previousMonth]) }}"
                                aria-label="เดือนก่อน">‹</a>
                            <strong>{{ $monthTitle }}</strong>
                            <a href="{{ route('booking_schedule', ['serviceId' => $service->service_id, 'month' => $nextMonth]) }}"
                                aria-label="เดือนถัดไป">›</a>
                        </div>
                    </div>
                    <p class="booking-intro">เลือกวันที่มีตารางช่าง แล้วตรวจสอบจำนวนช่างที่ยังว่างในแต่ละเวลา</p>
                    <table class="booking-calendar">
                        <thead>
                            <tr>
                                <th>จ.</th>
                                <th>อ.</th>
                                <th>พ.</th>
                                <th>พฤ.</th>
                                <th>ศ.</th>
                                <th>ส.</th>
                                <th>อา.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($calendarRows as $week)
                            <tr>
                                @foreach ($week as $cell)
                                <td>
                                    @if ($cell && $cell['available'])
                                    <a class="calendar-day available {{ $cell['selected'] ? 'selected' : '' }}"
                                        href="{{ route('booking_schedule', ['serviceId' => $service->service_id, 'month' => $monthKey, 'date' => $cell['date']]) }}">{{ $cell['day'] }}</a>
                                    @elseif ($cell)
                                    <span class="calendar-day">{{ $cell['day'] }}</span>
                                    @else
                                    <span class="calendar-day empty"></span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (empty($schedules))<p class="booking-calendar-note">เดือนนี้ยังไม่มีวันที่เปิดให้จอง</p>@endif
                </div>

                <div class="booking-panel">
                    <h2>เลือกเวลาที่ให้บริการที่สะดวก ⏱️</h2>
                    <p class="booking-intro">@if ($selectedDate)เวลาว่างของวันที่ {{ $selectedDateLabel }}@else
                        กรุณาเลือกวันที่มีวงกลมในปฏิทินก่อน @endif</p>
                    @if (session('booking_error'))<p class="pet-error">{{ session('booking_error') }}</p>@endif
                    @if (! $selectedDate)
                    <div class="booking-empty">เลือกวันก่อน แล้วเวลาที่ว่างจะแสดงที่นี่</div>
                    @elseif (empty($daySchedules))
                    <div class="booking-empty">วันนี้ไม่มีเวลาว่าง กรุณาเลือกวันอื่น</div>
                    @else
                    @if (! $hasAvailableSlot)<div class="booking-empty booking-full-note">ช่างเต็มทุกช่วงเวลาแล้ว
                        กรุณาเลือกวันหรือเวลาอื่น</div>@endif
                    <form id="booking-schedule-form" method="POST"
                        action="{{ route('booking_schedule_store', $service->service_id) }}">
                        @csrf
                        <div class="booking-slot-list">
                            @foreach ($daySchedules as $slot)
                            <label class="booking-slot {{ $slot['available_staff_count'] === 0 ? 'is-full' : '' }}">
                                <input type="radio" name="slot"
                                    value="{{ $slot['work_date']->format('Y-m-d') }}|{{ $slot['start_time'] }}" required
                                    @disabled($slot['available_staff_count']===0)>
                                <span><strong>{{ $slot['start_time'] }} น.</strong><small>ถึง {{ $slot['end_time'] }}
                                        น.</small><small
                                        class="slot-capacity">{{ $slot['available_staff_count'] > 0 ? 'ช่างว่าง ' . $slot['available_staff_count'] . ' คน' : 'ช่างเต็มแล้ว' }}</small></span>
                            </label>
                            @endforeach
                        </div>
                        @error('slot')<p class="pet-error">กรุณาเลือกเวลา</p>@enderror
                    </form>
                    @endif
                </div>
            </section>
            <div class="booking-side">
                <aside class="booking-summary">
                    <h2>สรุปรายการจอง</h2>
                    <div class="booking-summary-row"><span class="booking-icon">@if ($pet->pet_image)<img
                                class="pet-avatar-image" src="{{ asset('storage/'.$pet->pet_image) }}"
                                alt="">@else{{ $pet->breed->petType->pet_type_name === 'แมว' ? '🐱' : '🐶' }}@endif</span>
                        <div><small>สัตว์เลี้ยง</small><strong>{{ $pet->pet_name }} ·
                                {{ $pet->breed->pet_breed_name }}</strong></div>
                    </div>
                    <div class="booking-summary-row"><span class="booking-icon service-icon">🫧</span>
                        <div><small>บริการที่เลือก</small><strong>{{ $service->service_name }}</strong></div>
                    </div>
                    <div class="booking-summary-row booking-date-placeholder">
                        <div>
                            <small>วันเวลา</small><strong>{{ $selectedDate ? $selectedDateLabel : 'ยังไม่ได้เลือกวันเวลา' }}</strong>
                        </div>
                    </div>
                    <div class="booking-total">
                        <span>ยอดชำระโดยประมาณ</span><strong>{{ number_format($price, 0) }}฿</strong></div>
                    <p class="booking-weight">น้ำหนัก {{ number_format((float) $pet->pet_weight, 2) }} กก. ·
                        คิดราคาตามช่วงน้ำหนัก</p>
                    <small class="booking-help">เวลาให้บริการอ้างอิงจากตารางเวลาของร้าน</small>
                    <div class="booking-actions booking-summary-actions"><a class="button button-outline"
                            href="{{ route('booking_show', ['serviceId' => $service->service_id, 'pet_id' => $pet->id]) }}">ย้อนกลับ</a><button
                            class="button" type="submit" form="booking-schedule-form" @disabled(! $selectedDate ||
                            empty($daySchedules) || ! $hasAvailableSlot)>ไปต่อ</button></div>
                </aside>
            </div>
        </div>
    </main>
</body>

</html>