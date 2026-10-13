<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>

<body class="auth-page">
    <main class="card">
        <a class="brand" href="{{ route('home') }}">
            <img class="paw" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}"
                alt="">
            Puffly House Grooming
        </a>

        <div class="columns">
            <aside class="story">
                <img class="story-image"
                    src="{{ asset('images/Gemini_Generated_Image_5asu0v5asu0v5asu_edited.jpg') }}?v={{ filemtime(public_path('images/Gemini_Generated_Image_5asu0v5asu0v5asu_edited.jpg')) }}"
                    alt="สุนัขและแมวในร้านอาบน้ำตัดขน">
                <div class="story-copy">
                    <h2>ดูแลสัตว์เลี้ยง<br>ด้วยความรักและใส่ใจ</h2>
                    <p>พื้นที่อบอุ่นสำหรับเจ้าตัวฟูและคนที่รักเขา</p>
                </div>
            </aside>

            <section class="content">
                <nav class="tabs">
                    <a class="tab active" href="{{ route('login') }}">🔑 เข้าสู่ระบบ</a>
                    <a class="tab" href="{{ route('register') }}">สมัครสมาชิก</a>
                </nav>
                <h1>เข้าสู่ระบบ</h1>
                <p class="intro">กรอกข้อมูลเพื่อเข้าใช้งานบัญชีของคุณ</p>

                @if (session('status'))
                <p class="status">{{ session('status') }}</p>
                @endif

                <form class="form" method="POST" action="{{ route('login_store') }}">
                    @csrf

                    <label for="email">อีเมล</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                        placeholder="name@example.com" required autofocus autocomplete="email">
                    @error('email')
                    <p class="error">{{ $message }}</p>
                    @enderror

                    <label for="password">รหัสผ่าน</label>
                    <input id="password" name="password" type="password" placeholder="กรอกรหัสผ่าน" required
                        autocomplete="current-password">
                    @error('password')
                    <p class="error">{{ $message }}</p>
                    @enderror

                    <button class="submit" type="submit">เข้าสู่ระบบ</button>
                </form>

                <p class="foot">
                    ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a>
                </p>
                <p class="back-home-wrap"><a class="back-home" href="{{ route('home') }}">กลับหน้าแรก</a></p>
            </section>
        </div>
    </main>
</body>

</html>