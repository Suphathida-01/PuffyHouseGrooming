<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service->service_name }} | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><img class="brand-icon" src="{{ asset('images/logo.jpeg') }}?v={{ filemtime(public_path('images/logo.jpeg')) }}" alt=""> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>

    <main class="service-detail-page">
        <a class="detail-back" href="{{ route('services') }}">← กลับไปบริการทั้งหมด</a>
        <section class="service-detail-card">
            <div class="detail-art">
                @if ($service->service_image_path)
                    <img class="service-detail-image" src="{{ asset('storage/'.$service->service_image_path) }}" alt="รูปบริการ {{ $service->service_name }}">
                @else
                    <span class="detail-paw-placeholder" aria-hidden="true">🐾</span>
                @endif
            </div>

            <div class="detail-info">
                <span class="detail-tag">PUFFLY CARE</span>
                <h1>{{ $service->service_name }}</h1>
                <p class="detail-starting">
                    @if ($startingPrice !== null)
                        เริ่มต้น {{ number_format($startingPrice, 0) }} บาท
                    @else
                        สอบถามราคา
                    @endif
                </p>

                <h2 class="detail-subheading">ราคาตามขนาด</h2>
                <div class="size-price-list">
                    <div><span>ขนาดเล็ก (ไม่เกิน 5 กก.)</span><strong>{{ (float) $service->price_small > 0 ? number_format((float) $service->price_small, 0).' บาท' : 'ยังไม่กำหนด' }}</strong></div>
                    <div><span>ขนาดกลาง (มากกว่า 5 ถึง 15 กก.)</span><strong>{{ (float) $service->price_medium > 0 ? number_format((float) $service->price_medium, 0).' บาท' : 'ยังไม่กำหนด' }}</strong></div>
                    <div><span>ขนาดใหญ่ (มากกว่า 15 กก.)</span><strong>{{ (float) $service->price_large > 0 ? number_format((float) $service->price_large, 0).' บาท' : 'ยังไม่กำหนด' }}</strong></div>
                </div>

                <h2 class="detail-subheading">รายละเอียดบริการ</h2>
                <p class="detail-description">{{ $service->service_description }}</p>
                <p class="detail-duration">ใช้เวลาประมาณ {{ $durationText }}</p>

                @guest
                    <a class="button detail-book-button" href="{{ route('login') }}">เข้าสู่ระบบเพื่อจองคิว　🐾</a>
                    <p class="detail-note">สามารถดูบริการได้โดยไม่ต้องเข้าสู่ระบบ</p>
                @else
                    @if (auth()->user()->user_role === 'customer')
                        <a class="button detail-book-button" href="{{ route('booking.show', $service->service_id) }}">จองคิวเลย　🐾</a>
                    @else
                        <a class="button detail-book-button" href="{{ route('dashboard') }}">กลับไปหลังบ้าน</a>
                    @endif
                    <p class="detail-note">เลือกสัตว์เลี้ยง วันเวลา และตรวจสอบข้อมูลก่อนส่งคำขอจอง</p>
                @endguest
            </div>
        </section>
    </main>
</body>
</html>
