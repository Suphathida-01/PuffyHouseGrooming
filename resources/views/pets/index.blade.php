<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สัตว์เลี้ยงของฉัน | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>

    <main class="pets-page">
        <div class="pets-heading">
            <div><h1>สัตว์เลี้ยงของฉัน</h1><p>จัดการข้อมูลและประวัติการรับบริการของน้อง ๆ</p></div>
            <a class="button" href="{{ route('pets.create') }}">＋ เพิ่มสัตว์เลี้ยง</a>
        </div>

        @if (session('success'))<p class="pet-success">{{ session('success') }}</p>@endif

        @if ($pets->isNotEmpty())
            <section class="pet-list">
                @foreach ($pets as $pet)
                    @php($lastBooking = $pet->bookings->sortByDesc('booking_date')->first())
                    <article class="pet-card">
                        <div class="pet-photo">
                            @if ($pet->pet_image)
                                <img class="pet-avatar-image" src="{{ asset('storage/'.$pet->pet_image) }}" alt="รูปสัตว์เลี้ยง {{ $pet->pet_name }}">
                            @else
                                {{ $pet->breed->petType->pet_type_name === 'แมว' ? '🐱' : '🐶' }}
                            @endif
                        </div>
                        <div class="pet-card-body">
                            <h2>{{ $pet->pet_name }}</h2>
                            <p class="pet-breed">{{ $pet->breed->petType->pet_type_name }} ({{ $pet->breed->pet_breed_name }})</p>
                            <div class="pet-facts"><span>{{ $pet->pet_birth ? 'อายุ '.$pet->pet_birth->age.' ปี' : 'ไม่ทราบวันเกิด' }}</span><span>น้ำหนัก {{ number_format((float) $pet->pet_weight, 2) }} กก.</span></div>
                            <div class="pet-last-visit"><small>รับบริการล่าสุด</small><strong>{{ $lastBooking ? $lastBooking->booking_date->format('d/m/Y') : 'ยังไม่มีประวัติการใช้บริการ' }}</strong></div>
                            <div class="pet-actions">
                                <a class="button pet-edit" href="{{ route('pets.edit', $pet->id) }}">✎ แก้ไข</a>
                                <form method="POST" action="{{ route('pets.destroy', $pet->id) }}">@csrf @method('DELETE')<button class="pet-delete" type="submit" aria-label="ลบ {{ $pet->pet_name }}">🗑</button></form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <section class="pets-empty"><div>🐾</div><h2>ยังไม่มีข้อมูลสัตว์เลี้ยง</h2><p>เพิ่มข้อมูลน้องก่อน เพื่อใช้ในการจองบริการ</p><a class="button" href="{{ route('pets.create') }}">＋ เพิ่มสัตว์เลี้ยงตัวแรก</a></section>
        @endif
    </main>
    <footer class="site-footer">© {{ date('Y') }} Puffly House Grooming · ดูแลด้วยความรักและใส่ใจ</footer>
</body>
</html>
