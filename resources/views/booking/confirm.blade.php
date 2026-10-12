<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ยืนยันการจอง | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
<header class="site-header">
    <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
    @include('partials.site-nav')
    @include('partials.account-menu')
</header>
<main class="booking-page">
    <div class="booking-steps"><span class="done"><b>✓</b> สัตว์เลี้ยง</span><i></i><span class="done"><b>✓</b> วันเวลา</span><i></i><span class="active"><b>3</b> ยืนยันข้อมูล</span><i></i><span><b>4</b> ชำระเงิน</span></div>
    <div class="booking-layout">
        <section class="booking-main">
            <form id="booking-confirm-form" method="POST" action="{{ route('booking.store', $service->service_id) }}">
                @csrf
                <div class="booking-panel">
                    <h1>ตรวจสอบความถูกต้องของการจอง 🪄</h1>
                    <div class="booking-review booking-review-pet"><span class="booking-pet-emoji">@if ($pet->pet_image)<img class="pet-avatar-image" src="{{ asset('storage/'.$pet->pet_image) }}" alt="">@else{{ $pet->breed->petType->pet_type_name === 'แมว' ? '🐱' : '🐶' }}@endif</span><div><strong>{{ $pet->pet_name }}</strong><span>{{ $pet->breed->petType->pet_type_name }} · {{ $pet->breed->pet_breed_name }} · {{ number_format((float) $pet->pet_weight, 2) }} กก. · {{ $service->service_name }} (เริ่มต้น {{ number_format($price, 0) }} บาท)</span></div></div>
                    <div class="booking-review-grid">
                        <div class="booking-review"><small>📅 วันเวลาที่นัดหมาย</small><strong>{{ \Illuminate\Support\Carbon::parse($workDate)->format('d/m/Y') }}</strong><span>เวลา {{ substr($bookingStart, 0, 5) }} น.</span></div>
                        <div class="booking-review"><small>🐾 บริการที่เลือก</small><strong>{{ $service->service_name }}</strong><span>ระยะเวลา {{ $service->service_duration_minutes }} นาที</span></div>
                    </div>
                    <h2 class="booking-subtitle">ข้อมูลผู้ติดต่อและรายละเอียดเพิ่มเติม 📞</h2>
                    <div class="booking-form-grid">
                        <div><label class="booking-label" for="contact_name">ชื่อ-นามสกุล เจ้าของ</label><input class="booking-input" id="contact_name" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" required>@error('contact_name')<p class="pet-error">{{ $message }}</p>@enderror</div>
                        <div><label class="booking-label" for="contact_phone">เบอร์โทรศัพท์ติดต่อ</label><input class="booking-input" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', auth()->user()->user_phone) }}" required>@error('contact_phone')<p class="pet-error">{{ $message }}</p>@enderror</div>
                    </div>
                    <label class="booking-label" for="booking_notes">ความต้องการเพิ่มเติม / หมายเหตุถึงสไตลิสต์</label>
                    <textarea class="booking-textarea" id="booking_notes" name="booking_notes" rows="3" maxlength="1000" placeholder="แจ้งรายละเอียดที่อยากให้ทางร้านทราบ (ไม่บังคับ)">{{ old('booking_notes') }}</textarea>
                    @error('booking_notes')<p class="pet-error">{{ $message }}</p>@enderror
                    <label class="booking-terms"><input type="checkbox" name="accept_terms" value="1" required @checked(old('accept_terms'))> ยืนยันว่าข้อมูลถูกต้องและยอมรับเงื่อนไขการให้บริการ และนโยบายความเป็นส่วนตัวของ Puffly House Grooming</label>
                    @error('accept_terms')<p class="pet-error">กรุณายืนยันข้อมูลก่อนส่งคำขอจอง</p>@enderror
                </div>
            </form>
        </section>
        <div class="booking-side">
            <aside class="booking-summary">
                <h2>สรุปรายการจอง</h2>
                <div class="booking-summary-row"><span class="booking-icon">@if ($pet->pet_image)<img class="pet-avatar-image" src="{{ asset('storage/'.$pet->pet_image) }}" alt="">@else{{ $pet->breed->petType->pet_type_name === 'แมว' ? '🐱' : '🐶' }}@endif</span><div><small>สัตว์เลี้ยง</small><strong>{{ $pet->pet_name }} · {{ $pet->breed->pet_breed_name }}</strong></div></div>
                <div class="booking-summary-row"><span class="booking-icon service-icon">🫧</span><div><small>บริการที่เลือก</small><strong>{{ $service->service_name }}</strong></div></div>
                <div class="booking-summary-row"><div><small>วันเวลา</small><strong>{{ \Illuminate\Support\Carbon::parse($workDate)->format('d/m/Y') }} · {{ substr($bookingStart, 0, 5) }} น.</strong></div></div>
                <div class="booking-total"><span>ยอดชำระโดยประมาณ</span><strong>{{ number_format($price, 0) }}฿</strong></div>
                <p class="booking-weight">น้ำหนัก {{ number_format((float) $pet->pet_weight, 2) }} กก. · {{ $pet->pet_weight <= 5 ? 'ไซซ์ S' : ($pet->pet_weight <= 15 ? 'ไซซ์ M' : 'ไซซ์ L') }}</p>
                <div class="booking-actions booking-summary-actions"><a class="button button-outline" href="{{ route('booking.schedule', $service->service_id) }}">ย้อนกลับ</a><button class="button" type="submit" form="booking-confirm-form">ยืนยันข้อมูลและไปชำระเงิน　➡️</button></div>
                <small class="booking-help">โปรดตรวจสอบข้อมูลให้ถูกต้องก่อนยืนยันและไปขั้นตอนชำระเงิน</small>
            </aside>
        </div>
    </div>
</main>
</body>
</html>
