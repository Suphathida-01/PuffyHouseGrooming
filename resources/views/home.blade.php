<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body class="home-body">
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>

    <main>
        <section class="hero" id="home">
            <div class="hero-copy">
                <span class="pill">🎁 พื้นที่แห่งความสุขของเจ้าตัวฟู</span>
                <h1>ดูแลน้องให้สะอาด น่ารัก<br>และมีความสุข <span>🐶 🐱</span></h1>
                <p>สัมผัสประสบการณ์การตัดแต่งขนสุดพรีเมียม สะอาด ปลอดภัย<br class="desktop-break"> ด้วยผลิตภัณฑ์อ่อนโยนและทีมงานที่ใส่ใจ</p>
                <a class="button" href="{{ route('services') }}">จองบริการเลย　›</a>
            </div>
           <div class="hero-art">
  <img class="hero-art-image" src="{{ asset('images/Related-Content-12-2.png') }}" alt="สุนัขที่ร้านตัดขน"></div>
        </section>

        <section class="services-section" id="services">
            <h2>ดูแลด้วยหัวใจและมาตรฐานที่ใส่ใจ</h2>
            <p class="section-intro">เลือกบริการที่เหมาะกับเจ้าตัวฟูของคุณ</p>
            <div class="service-cards">
                <article class="service-card"><span>🛁</span><h3>อาบน้ำและเป่าขน</h3><p>ทำความสะอาดอย่างอ่อนโยน ให้ขนนุ่มหอมสดชื่น</p></article>
                <article class="service-card"><span>✂️</span><h3>ตัดแต่งขน</h3><p>จัดทรงให้เหมาะกับน้อง พร้อมดูแลอย่างใจเย็น</p></article>
                <article class="service-card"><span>🐾</span><h3>ดูแลเล็บและหู</h3><p>ใส่ใจรายละเอียดเล็ก ๆ เพื่อสุขอนามัยที่ดี</p></article>
            </div>
        </section>
        <section class="contact-section" id="contact">
            <h2>พร้อมดูแลเจ้าตัวฟูของคุณ</h2>
            <p class="section-intro">สอบถามรายละเอียดหรือจองบริการกับเราได้ที่ช่องทางติดต่อ</p>
            <h3 class="team-heading">สมาชิกผู้จัดทำ</h3>
            <div class="team-cards">
                <article class="team-card"><img class="team-card-photo" src="{{ asset('images/team/member-1.webp') }}" alt="สมาชิกทีมคนที่ 1"><strong>นางสาวศุภธิดา ศุภเกษ</strong></article>
                <article class="team-card"><img class="team-card-photo" src="{{ asset('images/team/member-2.webp') }}" alt="สมาชิกทีมคนที่ 2"><strong>นายธนพัฒน์ เก้านพรัตน์</strong></article>
                <article class="team-card"><img class="team-card-photo" src="{{ asset('images/team/member-3.webp') }}" alt="สมาชิกทีมคนที่ 3"><strong>นางสาวนัทจกร สิงห์ธวัช</strong></article>
                <article class="team-card"><img class="team-card-photo" src="{{ asset('images/team/member-4.webp') }}" alt="สมาชิกทีมคนที่ 4"><strong>นางสาวกนกรก มุ่งมีพฤทธฺิ์</strong></article>
            </div>
            <a class="button contact-cta" href="{{ route('services') }}">ดูบริการของเรา</a>
        </section>
    </main>
</body>
</html>
