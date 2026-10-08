<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บริการของเรา | Puffly House Grooming</title>
    <link rel="stylesheet" href="{{ asset('css/puffly.css') }}?v={{ filemtime(public_path('css/puffly.css')) }}">
</head>
<body>
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}"><span class="brand-icon">🐾</span> Puffly House <span>Grooming</span></a>
        @include('partials.site-nav')
        @include('partials.account-menu')
    </header>

    <main class="services-page">
        <section class="services-heading">
            <span class="section-kicker">ใส่ใจทุกขั้นตอนการดูแล</span>
            <h1>บริการทั้งหมดของเรา</h1>
            <p>เลือกบริการที่เหมาะกับน้อง ๆ แล้วเลือกรูปแบบและขนาดในขั้นตอนการจอง</p>
        </section>

        @if ($services->isNotEmpty())
            <section class="service-list" aria-label="รายการบริการ">
                @foreach ($services as $service)
                    <article class="service-item">
                        <div class="service-picture">
                            <span class="service-picture-icon">{{ $loop->iteration % 3 === 1 ? '🛁' : ($loop->iteration % 3 === 2 ? '✂️' : '🐾') }}</span>
                            <span class="picture-caption">Puffly Care</span>
                        </div>
                        <div class="service-details">
                            <div class="service-title-row">
                                <h2>{{ $service->service_name }}</h2>
                                <span class="service-duration">{{ $service->duration_text }}</span>
                            </div>
                            <p>{{ $service->service_description }}</p>
                            <div class="service-bottom">
                                <strong class="service-price">
                                    @if ($service->starting_price !== null)
                                        เริ่มต้น {{ number_format($service->starting_price, 0) }} บาท
                                    @else
                                        สอบถามราคา
                                    @endif
                                </strong>
                                <a class="button button-small" href="{{ route('services.show', $service->service_id) }}">ดูรายละเอียด</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <div class="services-empty">ยังไม่มีบริการที่เปิดให้จองในขณะนี้</div>
        @endif
    </main>
    <footer class="site-footer">© {{ date('Y') }} Puffly House Grooming · ดูแลด้วยความรักและใส่ใจ</footer>
</body>
</html>
