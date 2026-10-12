<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จองบริการ | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
<header class="site-header">
    <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
    @include('partials.site-nav')
    @include('partials.account-menu')
</header>
<main class="booking-page">
    <div class="booking-steps"><span class="active"><b>1</b> สัตว์เลี้ยง</span><i></i><span><b>2</b> วันเวลา</span><i></i><span><b>3</b> ยืนยันข้อมูล</span><i></i><span><b>4</b> ชำระเงิน</span></div>
    <div class="booking-layout">
        <section class="booking-main">
            @if ($pets->isEmpty())
                <div class="booking-panel"><h1>เลือกสัตว์เลี้ยงของคุณ 🐕</h1><p class="booking-intro">เพิ่มข้อมูลสัตว์เลี้ยงก่อน เพื่อใช้ในการจองบริการ</p><a class="button" href="{{ route('pets.create', ['booking_service_id' => $service->service_id]) }}">＋ เพิ่มสัตว์เลี้ยง</a></div>
            @else
                <div class="booking-panel">
                    <h1>เลือกสัตว์เลี้ยงของคุณ 🐕</h1>
                    <p class="booking-intro">เลือกสัตว์เลี้ยงที่ต้องการใช้บริการ รายละเอียดจะถูกนำไปประกอบการจอง</p>
                    <div class="booking-pet-list">
                        @foreach ($pets as $item)
                            <a class="booking-pet-option {{ $selectedPet && $selectedPet->id === $item->id ? 'selected' : '' }}" href="{{ route('booking.show', ['serviceId' => $service->service_id, 'pet_id' => $item->id]) }}">
                                <span class="booking-pet-emoji">
                                    @if ($item->pet_image)
                                        <img class="pet-avatar-image" src="{{ asset('storage/'.$item->pet_image) }}" alt="">
                                    @else
                                        {{ $item->breed->petType->pet_type_name === 'แมว' ? '🐱' : '🐶' }}
                                    @endif
                                </span>
                                <span class="booking-pet-text"><strong>{{ $item->pet_name }}</strong><small>{{ $item->breed->petType->pet_type_name }} · {{ $item->breed->pet_breed_name }} · {{ $item->pet_birth ? $item->pet_birth->age.' ปี' : 'ไม่ทราบวันเกิด' }} · น้ำหนัก {{ number_format((float) $item->pet_weight, 2) }} กก.</small></span>
                                <span class="booking-check">✓</span>
                            </a>
                        @endforeach
                    </div>
                    @error('pet_id')<p class="pet-error">กรุณาเลือกสัตว์เลี้ยง</p>@enderror
                </div>
                @if ($pet)
                    <form id="pet-choice-form" method="POST" action="{{ route('booking.pet', $service->service_id) }}">
                        @csrf
                        <input type="hidden" name="pet_id" value="{{ $pet->id }}">
                    </form>
                @endif
                <div class="booking-panel booking-add-pet"><h2>＋ ลงทะเบียนสัตว์เลี้ยงใหม่</h2><p>เพิ่มข้อมูลน้องก่อน แล้วกลับมาจองบริการนี้ต่อได้</p><a class="button button-outline" href="{{ route('pets.create', ['booking_service_id' => $service->service_id]) }}">เพิ่มสัตว์เลี้ยง</a></div>
            @endif
        </section>
        <div class="booking-side">
            <aside class="booking-summary">
                <h2>สรุปรายการจอง</h2>
                <div class="booking-summary-row"><span class="booking-icon service-icon">🫧</span><div><small>บริการที่เลือก</small><strong>{{ $service->service_name }}</strong></div></div>
                <div class="booking-summary-row"><span class="booking-icon">🐾</span><div><small>สัตว์เลี้ยง</small><strong>เลือกสัตว์เลี้ยง</strong></div></div>
                <button class="button booking-next" type="submit" form="pet-choice-form" @disabled(! $pet)>ขั้นตอนถัดไป (เลือกวันเวลา)　➡️</button>
                <small class="booking-help">เลือกสัตว์เลี้ยงก่อนเพื่อดำเนินการต่อ</small>
            </aside>
        </div>  </div>
</main>
</body>
</html>
