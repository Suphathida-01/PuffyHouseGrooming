<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>หน้าหลักผู้ดูแลระบบ | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
</head>
<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main">
        <header class="admin-topbar"><div><h1>หน้าหลัก</h1><p class="admin-subtitle">ภาพรวมลูกค้า สัตว์เลี้ยง การจอง และการทำงานของร้าน</p></div></header>
        <section class="admin-metrics" aria-label="สถิติภาพรวม">
            <article class="metric-card"><span class="metric-icon purple">♙</span><small>จำนวนลูกค้าทั้งหมด</small><strong>{{ number_format($totalCustomers) }} <em>ราย</em></strong></article>
            <article class="metric-card"><span class="metric-icon pink">🐾</span><small>จำนวนสัตว์เลี้ยง</small><strong>{{ number_format($totalPets) }} <em>ตัว</em></strong></article>
            <article class="metric-card"><span class="metric-icon blue">▣</span><small>การจองทั้งหมด ปี {{ $displayYear }}</small><strong>{{ number_format($totalBookings) }} <em>ครั้ง</em></strong></article>
        </section>
        <div class="admin-lower-grid">
            <section class="admin-panel" id="service-ranking">
                <div class="panel-heading"><div><h2>บริการยอดนิยม</h2><p>นับจากรายการจองที่ยังไม่ถูกยกเลิกในปี {{ $displayYear }}</p></div></div>
                @if ($popularServices->isEmpty())
                    <p class="empty-state">ยังไม่มีข้อมูลบริการ</p>
                @else
                    <ol class="service-ranking">
                        @foreach ($popularServices as $index => $service)
                            <li><div class="ranking-top"><strong><span>{{ $index + 1 }}</span>{{ $service->service_name }}</strong><small>{{ number_format($service->bookings_count) }} ครั้ง</small></div></li>
                        @endforeach
                    </ol>
                @endif
            </section>
            <section class="admin-panel status-panel">
                <div class="panel-heading"><div><h2>สรุปการจอง</h2><p>สถานะรายการจองในปี {{ $displayYear }}</p></div></div>
                <div class="status-bar-list">
                    @foreach ($bookingStatusSummary as $status)
                        <div class="status-bar-row"><span>{{ $status['label'] }}</span><div class="status-track"><div class="status-fill {{ $status['color'] }}" style="width: {{ $status['percent'] }}%"></div></div><strong>{{ $status['percent'] }}%</strong></div>
                    @endforeach
                </div>
                <ul class="status-legend">
                    @foreach ($bookingStatusSummary as $status)
                        <li><span class="status-dot {{ $status['color'] }}"></span><span>{{ $status['label'] }}</span><strong>{{ number_format($status['total']) }}</strong><small>{{ $status['percent'] }}%</small></li>
                    @endforeach
                </ul>
            </section>
        </div>
    </main>
</body>
</html>
