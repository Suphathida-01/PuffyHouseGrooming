<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ข้อมูลส่วนตัว | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
<header class="site-header">
    <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
    @include('partials.site-nav')
    @include('partials.account-menu')
</header>
<main class="profile-page">
    <div class="profile-heading"><span class="section-kicker">บัญชีของฉัน</span><h1>ข้อมูลส่วนตัว</h1><p>แก้ไขข้อมูลติดต่อและจัดการรหัสผ่านของคุณ</p></div>

    <section class="profile-card">
        <h2>ข้อมูลบัญชี</h2>
        <p class="profile-card-intro">ข้อมูลนี้ใช้สำหรับติดต่อและประกอบการจองบริการ</p>
        @if (session('profile_success'))<p class="pet-success">{{ session('profile_success') }}</p>@endif
        <form method="POST" action="{{ route('admin.profile.update') }}" class="profile-form">
            @csrf @method('PUT')
            <div><label for="name">ชื่อ-นามสกุล</label><input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" maxlength="255" required>@error('name')<p class="pet-error">{{ $message }}</p>@enderror</div>
            <div><label for="email">อีเมล</label><input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" maxlength="255" required>@error('email')<p class="pet-error">{{ $message }}</p>@enderror</div>
            <div><label for="user_phone">เบอร์โทรศัพท์</label><input id="user_phone" name="user_phone" type="tel" value="{{ old('user_phone', auth()->user()->user_phone) }}" maxlength="20" autocomplete="tel">@error('user_phone')<p class="pet-error">{{ $message }}</p>@enderror</div>
            <div class="profile-actions"><button class="button" type="submit">บันทึกข้อมูล</button></div>
        </form>
    </section>

    <section class="profile-card">
        <h2>เปลี่ยนรหัสผ่าน</h2>
        <p class="profile-card-intro">ตั้งรหัสผ่านใหม่อย่างน้อย 8 ตัวอักษร</p>
        @if (session('password_success'))<p class="pet-success">{{ session('password_success') }}</p>@endif
        <form method="POST" action="{{ route('admin.profile.password.update') }}" class="profile-form">
            @csrf @method('PUT')
            <div><label for="current_password">รหัสผ่านปัจจุบัน</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required>@error('current_password')<p class="pet-error">{{ $message }}</p>@enderror</div>
            <div><label for="password">รหัสผ่านใหม่</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>@error('password')<p class="pet-error">{{ $message }}</p>@enderror</div>
            <div><label for="password_confirmation">ยืนยันรหัสผ่านใหม่</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></div>
            <div class="profile-actions"><button class="button" type="submit">เปลี่ยนรหัสผ่าน</button></div>
        </form>
    </section>
</main>
<footer class="site-footer">© {{ date('Y') }} Puffly House Grooming · ดูแลด้วยความรักและใส่ใจ</footer>
</body>
</html>
