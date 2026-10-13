<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายงานและสรุปข้อมูล | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
</head>
<body class="admin-dashboard">
    @include('partials.admin-sidebar')

    <main class="admin-main">
        <header class="admin-topbar">
            <div><h1>หน้าหลัก</h1><p class="admin-subtitle">ภาพรวมข้อมูลลูกค้า สัตว์เลี้ยง การจอง และภาพรวมการดำเนินงานของร้าน</p></div>
            <div class="admin-date">{{ $displayDate }}</div>
        </header>

        <section class="admin-metrics" aria-label="สถิติภาพรวม">
            <article class="metric-card" id="customer-summary"><span class="metric-icon purple">♙</span><small>จำนวนลูกค้าทั้งหมด</small><strong>{{ number_format($totalCustomers) }} <em>ราย</em></strong></article>
            <article class="metric-card"><span class="metric-icon pink">🐾</span><small>จำนวนสัตว์เลี้ยง</small><strong>{{ number_format($totalPets) }} <em>ตัว</em></strong></article>
            <article class="metric-card" id="booking-summary"><span class="metric-icon blue">▣</span><small>การจองทั้งหมด ปี {{ $selectedYear }}</small><strong>{{ number_format($totalBookings) }} <em>ครั้ง</em></strong></article>
            <article class="metric-card"><span class="metric-icon green">฿</span><small>รายได้ที่ชำระแล้ว ปี {{ $selectedYear }}</small><strong>฿{{ number_format((float) $totalRevenue, 0) }}</strong></article>
        </section>

        <section class="admin-panel revenue-panel" id="revenue-chart">
            <div class="panel-heading"><div><h2>สรุปรายได้ประจำปี {{ $selectedYear }}</h2><p>ยอดรายเดือนจากรายการชำระเงินที่ยืนยันแล้ว</p></div><form class="month-picker" method="GET" action="{{ route('superadmin_dashboard') }}"><label for="revenue-year">เลือกปี ค.ศ.</label><input id="revenue-year" type="number" name="year" min="2000" max="{{ $maxYear }}" value="{{ $selectedYear }}"><button type="submit">แสดง</button></form></div>
            <div class="revenue-chart-layout">
                <span class="chart-y-axis">รายได้ (บาท)</span>
                <div class="revenue-chart-content">
                    <div class="revenue-chart" role="img" aria-label="กราฟรายได้ประจำปี {{ $selectedYear }} แกนตั้งแสดงรายได้เป็นบาท แกนนอนแสดงเดือน">
                        @foreach ($monthlyRevenue as $month)
                            <div class="chart-column"><span class="chart-value">฿{{ number_format($month['total'], 0) }}</span><div class="chart-track"><div class="chart-bar" style="height: {{ $month['height'] }}%"></div></div><small>{{ $month['label'] }}</small></div>
                        @endforeach
                    </div>
                    <span class="chart-x-axis">เดือน</span>
                </div>
            </div>
        </section>

        <div class="admin-lower-grid">
            <section class="admin-panel" id="service-ranking">
                <div class="panel-heading"><div><h2>บริการยอดนิยม</h2><p>นับจากรายการจองที่ยังไม่ถูกยกเลิกในปี {{ $selectedYear }}</p></div></div>
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
                <div class="panel-heading"><div><h2>สรุปการจอง</h2><p>สถานะรายการจองในปี {{ $selectedYear }}</p></div></div>
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
