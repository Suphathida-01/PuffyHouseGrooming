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
        <a class="site-brand" href="{{ route('home') }}"><span class="brand-icon">🐾</span> Puffly House <span>Grooming</span></a>
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
                <div class="hero-stats"><div><strong>10,000+</strong><small>น้อง ๆ ที่ไว้วางใจ</small></div><div><strong>5.0 ★</strong><small>รีวิวจากผู้ใช้บริการ</small></div><div><strong>100%</strong><small>ใส่ใจความปลอดภัย</small></div></div>
            </div>
            <div class="hero-art" aria-label="ภาพประกอบสุนัขที่ร้านตัดขน"><div class="art-sparkle">✦</div><div class="art-cloud"></div><div class="art-dog">🐕</div><div class="art-bubble">ฟูสะอาด<br>สดใส!</div><div class="art-caption">Puffly House Grooming</div></div>
        </section>

        <section class="services-section" id="services">
            <span class="section-kicker">บริการของเรา</span>
            <h2>ดูแลด้วยหัวใจและมาตรฐานที่ใส่ใจ</h2>
            <p class="section-intro">เลือกบริการที่เหมาะกับเจ้าตัวฟูของคุณ</p>
            <div class="service-cards">
                <article class="service-card"><span>🛁</span><h3>อาบน้ำและเป่าขน</h3><p>ทำความสะอาดอย่างอ่อนโยน ให้ขนนุ่มหอมสดชื่น</p></article>
                <article class="service-card"><span>✂️</span><h3>ตัดแต่งขน</h3><p>จัดทรงให้เหมาะกับน้อง พร้อมดูแลอย่างใจเย็น</p></article>
                <article class="service-card"><span>🐾</span><h3>ดูแลเล็บและหู</h3><p>ใส่ใจรายละเอียดเล็ก ๆ เพื่อสุขอนามัยที่ดี</p></article>
            </div>
        </section>
        <section class="contact-section" id="contact">
            <span class="section-kicker">ติดต่อเรา</span>
            <h2>พร้อมดูแลเจ้าตัวฟูของคุณ</h2>
            <p class="section-intro">สอบถามรายละเอียดหรือจองบริการกับเราได้ที่ช่องทางติดต่อ</p>
            <div class="contact-cards">
                <article class="contact-card"><strong>โทรศัพท์</strong><span>เพิ่มเบอร์โทรร้าน</span></article>
                <article class="contact-card"><strong>LINE / โซเชียล</strong><span>เพิ่มช่องทางติดต่อร้าน</span></article>
            </div>
            <h3 class="team-heading">สมาชิกผู้จัดทำ</h3>
            <div class="team-cards">
                <article class="team-card"><span>🐾</span><strong>ชื่อสมาชิก 1</strong><small>หน้าที่รับผิดชอบ</small></article>
                <article class="team-card"><span>🐾</span><strong>ชื่อสมาชิก 2</strong><small>หน้าที่รับผิดชอบ</small></article>
                <article class="team-card"><span>🐾</span><strong>ชื่อสมาชิก 3</strong><small>หน้าที่รับผิดชอบ</small></article>
            </div>
            <a class="button contact-cta" href="{{ route('services') }}">ดูบริการของเรา</a>
        </section>
    </main>
    <footer class="site-footer">© {{ date('Y') }} Puffly House Grooming · ดูแลด้วยความรักและใส่ใจ</footer>
</body>
</html>
