<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เลือกประเภทสัตว์ | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><span class="brand-icon">🐾</span> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>
    <main class="pet-form-page">
        <a class="detail-back" href="{{ route('pets.index') }}">← กลับไปสัตว์เลี้ยงของฉัน</a>
        <section class="pet-form-card pet-type-step">
            <h1>เพิ่มสัตว์เลี้ยง</h1>
            <p class="pet-form-intro">เริ่มจากเลือกประเภทสัตว์เลี้ยงของคุณ</p>
            @if (session('type_error'))<p class="pet-error">{{ session('type_error') }}</p>@endif
            <form class="pet-form" method="GET" action="{{ route('pets.create.form') }}">
                <label for="pet_type_id">ประเภทสัตว์</label>
                <select id="pet_type_id" name="pet_type_id">
                    <option value="">เลือกประเภทสัตว์</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->pet_type_id }}">{{ $type->pet_type_name }}</option>
                    @endforeach
                </select>
                <p class="pet-type-help">เลือกประเภทก่อน แล้วหน้าถัดไปจะมีสายพันธุ์ให้เลือก</p>
                <div class="pet-form-actions"><a class="pet-cancel" href="{{ route('pets.index') }}">ยกเลิก</a><button class="button" type="submit">ไปต่อ</button></div>
            </form>
        </section>
    </main>
</body>
</html>
