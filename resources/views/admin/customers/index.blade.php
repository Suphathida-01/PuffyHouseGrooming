<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการลูกค้า | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=10">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
            <a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a><a href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a><a class="active" href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a><a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a><a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a><a href="{{ route('admin.payments') }}"><span>▥</span>รายงานยอดขาย</a>
        </nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar customer-page-heading">
            <div><p class="admin-eyebrow">ข้อมูลสมาชิก</p><h1>จัดการลูกค้า</h1><p class="admin-subtitle">ฐานข้อมูลลูกค้าและประวัติการใช้บริการ</p></div>
            <div class="customer-tools">
                <form class="customer-search" method="GET" action="{{ route('admin.customers.index') }}"><input id="customer-search" type="search" name="search" value="{{ $search }}" placeholder="ชื่อ อีเมล หรือเบอร์โทร" aria-label="ค้นหาตามชื่อ อีเมล หรือเบอร์โทร"><button type="submit" aria-label="ค้นหา">⌕</button></form>
            </div>
        </header>
        @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
        <section class="admin-panel booking-table-panel"><div class="booking-table-wrap">
            <table class="booking-table customer-table">
                <thead><tr><th>ลำดับ</th><th>ชื่อ-นามสกุล</th><th>อีเมล</th><th>เบอร์โทร</th><th>สัตว์เลี้ยง (จำนวน)</th><th>วันที่สมัคร</th><th>การจัดการ</th></tr></thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        @php $thaiMonths = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.']; $registered = strtotime($customer->created_at); @endphp
                        <tr><td>{{ $index + 1 }}</td><td><strong>{{ $customer->name }}</strong></td><td>{{ $customer->email }}</td><td>{{ $customer->user_phone ?: '—' }}</td><td><span class="customer-pet-count">{{ $customer->pet_count }} ตัว</span></td><td>{{ date('d', $registered) }} {{ $thaiMonths[(int) date('n', $registered)] }} {{ date('Y', $registered) + 543 }}</td>
                            <td><div class="booking-actions"><a class="booking-action" href="{{ route('admin.customers.show', $customer->customer_id) }}">ดู</a><a class="booking-action outline" href="{{ route('admin.customers.edit', $customer->customer_id) }}">แก้ไข</a><form method="POST" action="{{ route('admin.customers.destroy', $customer->customer_id) }}" onsubmit="return confirm('นำข้อมูลลูกค้ารายนี้ออกจากรายการหรือไม่?')">@csrf @method('DELETE')<button class="booking-action danger" type="submit">ลบ</button></form></div></td></tr>
                    @empty
                        <tr><td class="booking-empty" colspan="7">{{ $search !== '' ? 'ไม่พบลูกค้าที่ตรงกับคำค้นหา' : 'ยังไม่มีข้อมูลลูกค้า' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></section>
    </main>
</body>
</html>
