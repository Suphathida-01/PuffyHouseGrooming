<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายละเอียดลูกค้า | Puffly House</title><link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=9">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ"><a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a><a href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a><a class="active" href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a><a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a><a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a><a href="{{ route('admin.payments') }}"><span>▥</span>รายงานยอดขาย</a></nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar"><div><p class="admin-eyebrow">ข้อมูลสมาชิก</p><h1>รายละเอียดลูกค้า</h1><p class="admin-subtitle">ข้อมูลบัญชี สัตว์เลี้ยง และประวัติการจอง</p></div><div class="customer-tools"><a class="booking-action" href="{{ route('admin.customers.index') }}">กลับ</a><a class="customer-add-button" href="{{ route('admin.customers.edit', $customer->customer_id) }}">แก้ไขข้อมูล</a></div></header>
        <section class="admin-panel customer-details"><h2>{{ $customer->user->name }}</h2><p><strong>อีเมล</strong>{{ $customer->user->email }}</p><p><strong>เบอร์โทร</strong>{{ $customer->user->user_phone ?: '—' }}</p><h3>สัตว์เลี้ยง ({{ $customer->pets->count() }})</h3>
            @forelse ($customer->pets as $pet)<p><strong>{{ $pet->pet_name }}</strong>{{ $pet->breed->pet_breed_name ?? '' }} {{ $pet->breed->petType->pet_type_name ?? '' }}</p>@empty<p>ยังไม่มีข้อมูลสัตว์เลี้ยง</p>@endforelse
        </section>
        <section class="admin-panel"><div class="panel-heading"><div><h2>ประวัติการจอง</h2><p>รายการล่าสุดไม่เกิน 10 รายการ</p></div></div><div class="booking-table-wrap"><table class="booking-table"><thead><tr><th>หมายเลขจอง</th><th>สัตว์เลี้ยง</th><th>บริการ</th><th>วันที่/เวลา</th><th>สถานะ</th></tr></thead><tbody>
            @forelse ($bookings as $booking)
                @php $statusLabels = ['pending' => 'รอยืนยัน', 'confirmed' => 'ยืนยันแล้ว', 'completed' => 'เสร็จสิ้น', 'cancelled' => 'ยกเลิก']; @endphp
                <tr><td>PH-{{ str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT) }}</td><td>{{ $booking->pet_name }}</td><td>{{ $booking->service_name }}</td><td>{{ date('d/m/Y', strtotime($booking->booking_date)) }} {{ substr($booking->booking_start_time, 0, 5) }}</td><td>{{ $statusLabels[$booking->booking_status] }}</td></tr>
            @empty<tr><td class="booking-empty" colspan="5">ยังไม่มีประวัติการจอง</td></tr>@endforelse
        </tbody></table></div></section>
    </main>
</body>
</html>
