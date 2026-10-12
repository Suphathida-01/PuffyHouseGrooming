<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ชำระเงิน | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
<header class="site-header">
    <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
    @include('partials.site-nav')
    @include('partials.account-menu')
</header>
<main class="payment-page">
    <div class="booking-steps"><span class="done"><b>✓</b> สัตว์เลี้ยง</span><i></i><span class="done"><b>✓</b> วันเวลา</span><i></i><span class="done"><b>✓</b> ยืนยันข้อมูล</span><i></i><span class="active"><b>4</b> ชำระเงิน</span></div>
    <section class="payment-card">
        <div class="payment-heading">
            <div><h1>ชำระเงินค่าบริการ</h1><p>เลือกช่องทางชำระเงินเพื่อส่งรายการให้ร้านตรวจสอบ</p></div>
            <div class="payment-reference"><small>หมายเลขจอง</small><strong>PH-{{ $booking->booking_date->format('Ymd') }}-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</strong></div>
        </div>
        <div class="payment-columns">
            <section class="payment-info-panel">
                <h2>สรุปรายละเอียดการจอง</h2>
                <div class="payment-pet-row"><span class="booking-pet-emoji">@if ($booking->pet->pet_image)<img class="pet-avatar-image" src="{{ asset('storage/'.$booking->pet->pet_image) }}" alt="">@else{{ $booking->pet->breed->petType->pet_type_name === 'แมว' ? '🐱' : '🐶' }}@endif</span><div><small>สัตว์เลี้ยง</small><strong>{{ $booking->pet->pet_name }} ({{ $booking->pet->breed->pet_breed_name }})</strong></div></div>
                <div class="payment-service-row"><small>บริการที่จอง</small><strong>{{ $booking->service->service_name }}</strong></div>
                <div class="payment-datetime-grid">
                    <div><small>วันที่รับบริการ</small><strong>{{ $booking->booking_date->format('d/m/Y') }}</strong></div>
                    <div><small>เวลานัดหมาย</small><strong>{{ substr($booking->booking_start_time, 0, 5) }} น.</strong></div>
                </div>
                @if ($booking->booking_notes)
                    <div class="payment-service-row"><small>หมายเหตุ</small><strong>{{ $booking->booking_notes }}</strong></div>
                @endif
                <div class="payment-total"><strong>ยอดชำระทั้งหมด</strong><strong>{{ number_format((float) $booking->booking_total_price, 0) }}฿</strong></div>
            </section>

            <section class="payment-method-panel">
                <h2>เลือกช่องทางการชำระเงิน</h2>
                <form method="POST" action="{{ route('payments.store', $booking->booking_id) }}">
                    @csrf
                    <div class="payment-method-options">
                        <div class="payment-choice">
                            <label class="payment-method-option"><input type="radio" name="payment_method" value="qrCode" @checked(old('payment_method', 'qrCode') === 'qrCode') required><span><strong>QR Code พร้อมเพย์</strong><small>สแกนเพื่อชำระเงิน</small></span></label>
                            <div class="payment-option-content"><div class="payment-qr-preview"><img class="payment-qr-image" src="{{ asset('images/promptpay-qr.jpg') }}" alt="QR PromptPay สำหรับโอนชำระค่าบริการ"><p><strong>สแกน QR เพื่อโอนชำระเงิน</strong><br>หลังโอนแล้ว รายการจองจะอยู่ในสถานะรอตรวจสอบ</p></div></div>
                        </div>
                        <div class="payment-choice">
                            <label class="payment-method-option"><input type="radio" name="payment_method" value="creditCard" @checked(old('payment_method') === 'creditCard')><span><strong>บัตรเครดิต / บัตรเดบิต</strong><small>ชำระผ่านผู้ให้บริการเมื่อเชื่อมต่อระบบแล้ว</small></span></label>
                        </div>
                        <div class="payment-choice">
                            <label class="payment-method-option"><input type="radio" name="payment_method" value="bankTransfer" @checked(old('payment_method') === 'bankTransfer')><span><strong>โอนผ่านธนาคาร</strong><small>ดูข้อมูลบัญชีสำหรับโอน</small></span></label>
                        </div>
                    </div>
                    @error('payment_method')<p class="pet-error">{{ $message }}</p>@enderror
                    <div class="payment-notice">QR Code จะรอ superadmin ตรวจสอบและยืนยัน ส่วนบัตรเครดิต/โอนผ่านธนาคารจะเปลี่ยนเป็น “ชำระแล้ว” ทันที โดยระบบยังไม่ได้เชื่อมต่อรับเงินจริง</div>
                    <div class="payment-actions"><a class="button button-outline" href="{{ route('pets.index') }}">ย้อนกลับ</a><button class="button" type="submit">ยืนยันช่องทางชำระเงิน</button></div>
                </form>
            </section>
        </div>
    </section>
</main>
</body>
</html>
