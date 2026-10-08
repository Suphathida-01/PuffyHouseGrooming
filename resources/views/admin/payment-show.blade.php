<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายละเอียดการชำระเงิน | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=23">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
            <a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a><a href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a><a href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a><a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a><a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a><a class="active" href="{{ route('admin.payments') }}"><span>▥</span>จัดการชำระเงิน</a>
        </nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>
    <main class="admin-main">
        @php
            $booking = $payment->booking;
            $reference = $payment->transaction_ref ?: 'TXN-' . str_pad((string) $payment->payment_id, 5, '0', STR_PAD_LEFT);
            $statusLabels = ['completed' => 'ชำระแล้ว', 'pending' => 'รอชำระ', 'failed' => 'ไม่สำเร็จ'];
            $statusClasses = ['completed' => 'status-paid', 'pending' => 'status-pending', 'failed' => 'status-refunded'];
            $methodLabels = ['qrCode' => 'PromptPay', 'creditCard' => 'บัตรเครดิต', 'bankTransfer' => 'โอนผ่านธนาคาร'];
            $bookingStatusLabels = ['pending' => 'รอยืนยัน', 'confirmed' => 'ยืนยันแล้ว', 'completed' => 'เสร็จสิ้น', 'cancelled' => 'ยกเลิก'];
        @endphp
        <div class="payment-detail-page">
            <header class="page-header">
                <div class="page-title"><p class="admin-eyebrow">รายการรับชำระเงิน</p><h1>รายละเอียดการชำระเงิน</h1><p>ตรวจสอบข้อมูลรายการและยืนยันยอดที่ได้รับ</p></div>
                <a class="staff-button staff-button-light" href="{{ route('admin.payments', ['date' => $payment->payment_date->format('Y-m-d')]) }}">← กลับรายการ</a>
            </header>

            @if (session('payment_error'))<div class="booking-notice error">{{ session('payment_error') }}</div>@endif

            <section class="payment-detail-card" aria-labelledby="payment-summary-title">
                    <div class="payment-detail-card-heading">
                        <div><p>หมายเลขรายการ</p><h2 id="payment-summary-title">{{ $reference }}</h2></div>
                        <span class="status-badge {{ $statusClasses[$payment->payment_status] ?? 'status-refunded' }}">{{ $statusLabels[$payment->payment_status] ?? $payment->payment_status }}</span>
                    </div>
                    <div class="payment-detail-amount"><span>จำนวนเงิน</span><strong>฿{{ number_format((float) $payment->payment_amount, 2) }}</strong></div>
                    <div class="payment-detail-information-grid">
                        <section class="payment-detail-group" aria-labelledby="payment-info-title">
                            <h3 id="payment-info-title">ข้อมูลการชำระเงิน</h3>
                            <dl class="payment-detail-list">
                                <div><dt>ลูกค้า</dt><dd>{{ $booking?->customer?->user?->name ?? 'ไม่พบข้อมูลลูกค้า' }}</dd></div>
                                <div><dt>บริการ</dt><dd>{{ $booking?->service?->service_name ?? 'ไม่พบข้อมูลบริการ' }}</dd></div>
                                <div><dt>ช่องทางการชำระ</dt><dd>{{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}</dd></div>
                                <div><dt>วันเวลารับรายการ</dt><dd>{{ $payment->payment_date->format('d/m/Y · H:i') }}</dd></div>
                            </dl>
                        </section>
                        <section class="payment-detail-group" aria-labelledby="booking-summary-title">
                            <h3 id="booking-summary-title">ข้อมูลการจอง</h3>
                    @if ($booking)
                        <dl class="payment-detail-list">
                            <div><dt>หมายเลขการจอง</dt><dd>PH-{{ str_pad((string) $booking->booking_id, 4, '0', STR_PAD_LEFT) }}</dd></div>
                            <div><dt>สัตว์เลี้ยง</dt><dd>{{ $booking->pet?->pet_name ?? 'ไม่พบข้อมูลสัตว์เลี้ยง' }}</dd></div>
                            <div><dt>วันเวลานัดหมาย</dt><dd>{{ $booking->booking_date->format('d/m/Y') }} · {{ substr($booking->booking_start_time, 0, 5) }}</dd></div>
                            <div><dt>สถานะการจอง</dt><dd>{{ $bookingStatusLabels[$booking->booking_status] ?? $booking->booking_status }}</dd></div>
                        </dl>
                    @else
                        <p class="staff-empty">ไม่พบรายการจองที่เชื่อมกับการชำระเงินนี้</p>
                    @endif
                        </section>
                    </div>
                    @if ($payment->payment_status === 'pending')
                        <form class="payment-detail-actions" method="POST" action="{{ route('admin.payments.confirm', $payment->payment_id) }}">
                            @csrf
                            <button class="payment-confirm-button" type="submit">ยืนยันว่าชำระแล้ว</button>
                        </form>
                    @endif
                </section>
        </div>
    </main>
</body>
</html>
