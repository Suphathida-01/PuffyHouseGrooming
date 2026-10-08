<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการการจอง | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=9">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
            <a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a>
            <a class="active" href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a>
            <a href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a>
            <a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a>
            <a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a>
            <a href="{{ route('admin.payments') }}"><span>▥</span>รายงานยอดขาย</a>
        </nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar booking-page-heading">
            <div><p class="admin-eyebrow">จัดการรายการ</p><h1>จัดการการจอง</h1><p class="admin-subtitle">ตรวจสอบและอัปเดตสถานะการนัดหมายอาบน้ำตัดขนสัตว์เลี้ยง</p></div>
            <form class="booking-filters" method="GET" action="{{ route('admin.bookings.index') }}">
                <label><span>วันที่จอง</span><input type="date" name="date" value="{{ $date }}"></label>
                <label><span>สถานะ</span><select name="status">
                    <option value="all" @selected($status === 'all')>สถานะทั้งหมด</option>
                    <option value="pending" @selected($status === 'pending')>รอยืนยัน</option>
                    <option value="confirmed" @selected($status === 'confirmed')>ยืนยันแล้ว</option>
                    <option value="completed" @selected($status === 'completed')>เสร็จสิ้น</option>
                    <option value="cancelled" @selected($status === 'cancelled')>ยกเลิก</option>
                </select></label>
                <button class="booking-filter-button" type="submit">ค้นหา</button>
                @if ($date !== '')<a class="booking-clear-date" href="{{ route('admin.bookings.index', ['status' => $status]) }}">ล้างวันที่</a>@endif
            </form>
        </header>

        @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
        @if (session('booking_error'))<div class="booking-notice error">{{ session('booking_error') }}</div>@endif
        @if ($errors->any())<div class="booking-notice error">{{ $errors->first() }}</div>@endif

        <section class="admin-panel booking-table-panel">
            <div class="booking-table-wrap">
                <table class="booking-table">
                    <thead><tr><th>หมายเลขจอง</th><th>ลูกค้า</th><th>สัตว์เลี้ยง</th><th>บริการ</th><th>วันที่/เวลา</th><th>ช่าง</th><th>สถานะ</th><th>การจัดการ</th></tr></thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            @php
                                $labels = ['pending' => 'รอยืนยัน', 'confirmed' => 'ยืนยันแล้ว', 'completed' => 'เสร็จสิ้น', 'cancelled' => 'ยกเลิก'];
                                $thaiMonths = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                                $dateParts = explode('-', $booking->booking_date);
                            @endphp
                            <tr>
                                <td><strong>PH-{{ str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $booking->customer_name }}</td>
                                <td>{{ $booking->pet_name }}</td>
                                <td>{{ $booking->service_name }}</td>
                                <td>{{ (int) $dateParts[2] }} {{ $thaiMonths[(int) $dateParts[1]] }} {{ substr($booking->booking_date, 0, 4) + 543 }} {{ substr($booking->booking_start_time, 0, 5) }}</td>
                                <td>{{ $booking->admin_name ?: 'ยังไม่ระบุ' }}</td>
                                <td><span class="booking-status {{ $booking->booking_status }}">{{ $labels[$booking->booking_status] }}</span></td>
                                <td><div class="booking-actions">
                                    @if ($booking->booking_status === 'pending')
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking->booking_id) }}">@csrf @method('PUT')<input type="hidden" name="booking_status" value="confirmed"><button class="booking-action primary" type="submit">ยืนยัน</button></form>
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking->booking_id) }}">@csrf @method('PUT')<input type="hidden" name="booking_status" value="cancelled"><button class="booking-action danger" type="submit">ยกเลิก</button></form>
                                    @elseif ($booking->booking_status === 'confirmed')
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking->booking_id) }}">@csrf @method('PUT')<input type="hidden" name="booking_status" value="completed"><button class="booking-action outline" type="submit">บันทึกว่าเสร็จแล้ว</button></form>
                                        <form method="POST" action="{{ route('admin.bookings.status', $booking->booking_id) }}">@csrf @method('PUT')<input type="hidden" name="booking_status" value="cancelled"><button class="booking-action danger" type="submit">ยกเลิก</button></form>
                                    @else
                                        <span class="booking-no-action">—</span>
                                    @endif
                                </div></td>
                            </tr>
                        @empty
                            <tr><td class="booking-empty" colspan="8">ไม่พบรายการจองตามเงื่อนไขที่เลือก</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
