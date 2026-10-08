<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายงานและสรุปข้อมูล | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=10">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
            <a class="active" href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a>
            <a href="{{ route('admin.bookings.index') }}"><span>▣</span>การจอง</a>
            <a href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a>
            <a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a>
            <a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a>
            <a href="{{ route('admin.payments') }}"><span>▥</span>จัดการชำระเงิน</a>
        </nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div><p class="admin-eyebrow">ภาพรวมร้าน</p><h1>รายงานและสรุปข้อมูล</h1><p class="admin-subtitle">ภาพรวมข้อมูลลูกค้า สัตว์เลี้ยง การจอง และรายได้ของร้าน</p></div>
            <div class="admin-date">{{ $displayDate }}</div>
        </header>

        <section class="admin-metrics" aria-label="สถิติภาพรวม">
            <article class="metric-card" id="customer-summary"><span class="metric-icon purple">♙</span><small>จำนวนลูกค้าทั้งหมด</small><strong>{{ number_format($totalCustomers) }} <em>ราย</em></strong></article>
            <article class="metric-card"><span class="metric-icon pink">🐾</span><small>จำนวนสัตว์เลี้ยง</small><strong>{{ number_format($totalPets) }} <em>ตัว</em></strong></article>
            <article class="metric-card" id="booking-summary"><span class="metric-icon blue">▣</span><small>การจองทั้งหมด</small><strong>{{ number_format($totalBookings) }} <em>ครั้ง</em></strong></article>
            <article class="metric-card"><span class="metric-icon green">฿</span><small>รายได้ที่ชำระแล้ว</small><strong>฿{{ number_format((float) $totalRevenue, 0) }}</strong></article>
        </section>

        <section class="admin-panel revenue-panel" id="revenue-chart">
            <div class="panel-heading"><div><h2>สรุปรายได้ย้อนหลัง 6 เดือน</h2><p>ยอดจากรายการชำระเงินที่ยืนยันแล้ว</p></div><form class="month-picker" method="GET" action="{{ route('dashboard') }}"><label for="revenue-month">เลือกเดือนสิ้นสุด</label><input id="revenue-month" type="month" name="month" value="{{ $selectedMonth }}" max="{{ $maxMonth }}"><button type="submit">แสดง</button></form></div>
            <div class="revenue-chart" role="img" aria-label="กราฟรายได้ย้อนหลัง 6 เดือน">
                @foreach ($monthlyRevenue as $month)
                    <div class="chart-column"><span class="chart-value">฿{{ number_format($month['total'], 0) }}</span><div class="chart-track"><div class="chart-bar" style="height: {{ $month['height'] }}%"></div></div><small>{{ $month['label'] }}</small></div>
                @endforeach
            </div>
        </section>

        <div class="admin-lower-grid">
            <section class="admin-panel" id="service-ranking">
                <div class="panel-heading"><div><h2>บริการยอดนิยม</h2><p>นับเฉพาะรายการที่ชำระเงินแล้วหรือเสร็จสิ้น</p></div></div>
                @if (count($popularServices) === 0)
                    <p class="empty-state">ยังไม่มีข้อมูลบริการ</p>
                @else
                    <ol class="service-ranking">
                        @foreach ($popularServices as $index => $service)
                            <li><div class="ranking-top"><strong><span>{{ $index + 1 }}</span>{{ $service->service_name }}</strong><small>{{ number_format($service->bookings_count) }} ครั้ง <b>฿{{ number_format((float) $service->bookings_sum_booking_total_price, 0) }}</b></small></div></li>
                        @endforeach
                    </ol>
                @endif
            </section>

            <section class="admin-panel status-panel">
                <div class="panel-heading"><div><h2>สรุปการจอง</h2><p>สถานะรายการจองในระบบ</p></div></div>
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
