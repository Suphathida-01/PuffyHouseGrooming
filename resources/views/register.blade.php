<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สมัครสมาชิก | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body class="auth-page">
    <main class="card">
        <a class="brand" href="{{ route('home') }}">
            <img class="paw" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt="">
            Puffly House Grooming
        </a>

        <div class="columns">
            <aside class="story">
                <span class="dog">🐩</span>
                <span class="tub"></span>
                <div class="story-copy">
                    <h2>ดูแลสัตว์เลี้ยง<br>ด้วยความรักและใส่ใจ</h2>
                    <p>พื้นที่อบอุ่นสำหรับเจ้าตัวฟูและคนที่รักเขา</p>
                </div>
            </aside>

            <section class="content">
                <nav class="tabs">
                    <a class="tab" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                    <a class="tab active" href="{{ route('register') }}">✍ สมัครสมาชิก</a>
                </nav>

                <p class="eyebrow">เริ่มต้นดูแลเจ้าตัวฟู</p>
                <h1>สร้างบัญชีใหม่</h1>
                <p class="intro">กรอกข้อมูลสั้น ๆ เพื่อสมัครสมาชิก</p>

                <form class="form" method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <label for="name">ชื่อ-นามสกุล <span style="color: red;">*</span></label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="ชื่อของคุณ"
                        required
                        autocomplete="name"
                    >
                    @error('name')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    <label for="email">อีเมล<span style="color: red;">*</span>
                </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="name@example.com"
                        required
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    <label for="phone">เบอร์โทรศัพท์ <span style="color: red;">*</span>
                    </label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        placeholder="08X-XXX-XXXX"
                        autocomplete="tel"
                    >
                    @error('phone')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    <label for="password">รหัสผ่าน  <span style="color: red;">*</span>
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="กรอกรหัสผ่าน"
                        required
                        autocomplete="new-password"
                    >
                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    <label for="password_confirmation">ยืนยันรหัสผ่าน <span style="color: red;">*</span></label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="กรอกรหัสผ่านอีกครั้ง"
                        required
                        autocomplete="new-password"
                    >

                    <button class="submit" type="submit">สมัครสมาชิก</button>
                </form>

                <p class="foot">
                    มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
                </p>
                <p class="back-home-wrap"><a class="back-home" href="{{ route('home') }}">← กลับหน้าแรก</a></p>
            </section>
        </div>
    </main>
</body>
</html>
