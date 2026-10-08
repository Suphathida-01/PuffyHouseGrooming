<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>การจองของฉัน | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
<header class="site-header">
    <a class="site-brand" href="{{ route('home') }}"><span class="brand-icon">🐾</span> Puffly House <span>Grooming</span></a>
    @include('partials.site-nav')
    @include('partials.account-menu')
</header>
<main class="my-bookings-page">
    <div class="my-bookings-heading">
        <div><span class="section-kicker">PUFFLY HOUSE GROOMING</span><h1>การจองของฉัน 🐾</h1><p>ดูนัดหมายที่กำลังจะมาถึงและประวัติการใช้บริการของน้อง ๆ</p></div>
        <a class="button" href="{{ route('services') }}">＋ จองบริการใหม่</a>
    </div>

    @if (session('success'))<p class="pet-success">{{ session('success') }}</p>@endif
    @if (session('booking_error'))<p class="pet-error">{{ session('booking_error') }}</p>@endif

    <section class="my-booking-stats" aria-label="สรุปรายการจอง">
        <article class="my-booking-stat"><span class="my-booking-stat-icon">📅</span><div><small>กำลังจะมาถึง</small><strong>{{ $upcomingCount }}</strong></div></article>
        <article class="my-booking-stat"><span class="my-booking-stat-icon is-complete">✓</span><div><small>ใช้บริการแล้ว</small><strong>{{ $completedCount }}</strong></div></article>
        <article class="my-booking-stat"><span class="my-booking-stat-icon is-pet">🐶</span><div><small>รายการทั้งหมด</small><strong>{{ $allBookings->count() }}</strong></div></article>
    </section>

    <section class="my-booking-list-panel">
        <div class="my-booking-list-heading"><h2>รายการนัดหมาย</h2><span>{{ $bookings->count() }} รายการ</span></div>
        <nav class="my-booking-filters" aria-label="กรองรายการจอง">
            <a class="{{ $filter === 'all' ? 'active' : '' }}" href="{{ route('my-bookings.index') }}">ทั้งหมด</a>
            <a class="{{ $filter === 'upcoming' ? 'active' : '' }}" href="{{ route('my-bookings.index', ['filter' => 'upcoming']) }}">กำลังจะมาถึง</a>
            <a class="{{ $filter === 'completed' ? 'active' : '' }}" href="{{ route('my-bookings.index', ['filter' => 'completed']) }}">เสร็จสิ้น</a>
            <a class="{{ $filter === 'cancelled' ? 'active' : '' }}" href="{{ route('my-bookings.index', ['filter' => 'cancelled']) }}">ยกเลิกแล้ว</a>
        </nav>

        @if ($bookings->isEmpty())
            <div class="my-bookings-empty"><span>🐾</span><h3>{{ $filter === 'all' ? 'ยังไม่มีรายการจอง' : 'ไม่พบรายการในหมวดนี้' }}</h3><p>เลือกบริการที่ต้องการ แล้วจองเวลาให้น้องได้เลย</p><a class="button" href="{{ route('services') }}">ดูบริการทั้งหมด</a></div>
        @else
            <div class="my-booking-cards">
                @foreach ($bookings as $booking)
                    @php
                        $pet = $booking->pet;
                        $payment = $booking->payments->sortByDesc('payment_id')->first();
                        $isCat = $pet->breed->petType->pet_type_name === 'แมว';
                        $statusLabels = ['pending' => 'รอยืนยัน', 'confirmed' => 'ยืนยันแล้ว', 'completed' => 'เสร็จสิ้น', 'cancelled' => 'ยกเลิกแล้ว'];
                        $statusLabel = $statusLabels[$booking->booking_status] ?? $booking->booking_status;
                        $statusClass = $booking->booking_status === 'cancelled' ? 'is-cancelled' : ($booking->booking_status === 'completed' ? 'is-complete' : ($booking->booking_status === 'confirmed' ? 'is-confirmed' : 'is-pending'));
                        $cancellationDeadline = $booking->booking_date->copy()->subDay()->endOfDay();
                        $canCancel = in_array($booking->booking_status, ['pending', 'confirmed'], true) && now()->lessThanOrEqualTo($cancellationDeadline) && $payment?->payment_status !== 'completed';
                        $thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                        $bookingDateLabel = $booking->booking_date->day.' '.$thaiMonths[$booking->booking_date->month].' '.($booking->booking_date->year + 543);
                    @endphp
                    <article class="my-booking-card" id="booking-{{ $booking->booking_id }}">
                        <div class="my-booking-card-top"><div><small>หมายเลขการจอง</small><strong>PH-{{ $booking->booking_date->format('Ymd') }}-{{ str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT) }}</strong></div><span class="my-booking-status {{ $statusClass }}"><i></i>{{ $statusLabel }}</span></div>
                        <div class="my-booking-card-main">
                            <span class="my-booking-pet-icon">{{ $isCat ? '🐱' : '🐶' }}</span>
                            <div class="my-booking-service"><strong>{{ $booking->service->service_name }}</strong><span>{{ $pet->pet_name }} · {{ $pet->breed->pet_breed_name }}</span><small>📅 {{ $bookingDateLabel }}　◷ {{ substr($booking->booking_start_time, 0, 5) }} น.　⏱ {{ $booking->service->service_duration_minutes }} นาที</small></div>
                            <div class="my-booking-price"><small>ยอดชำระ</small><strong>{{ number_format((float) $booking->booking_total_price, 0) }}฿</strong></div>
                        </div>
                        <div class="my-booking-card-bottom"><span>{{ $booking->booking_status === 'cancelled' ? 'ยกเลิกรายการจองแล้ว' : ($payment?->payment_status === 'completed' ? 'ชำระเงินแล้ว' : ($payment?->payment_status === 'failed' ? 'ยังไม่มีการชำระเงิน' : ($payment ? 'การชำระเงินรอตรวจสอบ' : 'บันทึกรายการจองแล้ว'))) }}</span>@if ($booking->booking_status !== 'cancelled' && $payment?->payment_status !== 'completed')<a href="{{ route('payments.create', $booking->booking_id) }}">ดูรายละเอียดการชำระเงิน　→</a>@endif</div>
                        @if ($canCancel)
                            <div class="booking-cancel-area"><details class="booking-cancel"><summary>ยกเลิกการจอง</summary><div class="booking-cancel-confirm"><p>ต้องการยกเลิกนัดหมายนี้ใช่ไหม? ยกเลิกได้ถึงวันก่อนวันนัด</p><div class="booking-cancel-actions"><form method="POST" action="{{ route('my-bookings.cancel', $booking->booking_id) }}">@csrf<button type="submit">ยืนยันยกเลิก</button></form><a href="{{ route('my-bookings.index', ['filter' => $filter, 'close_cancel' => $booking->booking_id]) }}#booking-{{ $booking->booking_id }}">กลับ</a></div></div></details></div>
                        @elseif ($payment?->payment_status === 'completed' && $booking->booking_status !== 'completed' && $booking->booking_status !== 'cancelled')
                            <p class="booking-cancel-note">รายการชำระเงินสำเร็จแล้ว หากต้องการยกเลิก กรุณาติดต่อร้าน</p>
                        @elseif (in_array($booking->booking_status, ['pending', 'confirmed'], true) && now()->greaterThan($cancellationDeadline))
                            <p class="booking-cancel-note">ยกเลิกได้ถึงวันก่อนวันนัดเท่านั้น</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
<footer class="site-footer">© {{ date('Y') }} Puffly House Grooming · ดูแลด้วยความรักและใส่ใจ</footer>
</body>
</html>
