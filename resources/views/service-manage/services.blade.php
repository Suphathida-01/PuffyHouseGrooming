<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการบริการ | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=9">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
            <a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a><a href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a><a href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a><a class="active" href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a><a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a><a href="{{ route('admin.payments') }}"><span>▥</span>รายงานยอดขาย</a>
        </nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar service-page-header"><div><p class="admin-eyebrow">ตั้งค่าร้าน</p><h1>จัดการบริการ</h1><p class="admin-subtitle">ตั้งค่าบริการ ราคาแยกตามขนาดสัตว์เลี้ยง และสถานะการเปิดให้บริการ</p></div><a class="service-button primary" href="{{ route('admin.services.create') }}">＋ เพิ่มบริการ</a></header>
        @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
        <section class="admin-panel booking-table-panel">
            <div class="booking-table-wrap"><table class="booking-table service-table">
                <thead><tr><th>ลำดับ</th><th>ชื่อบริการ</th><th>รายละเอียด</th><th>ราคา (เล็ก / กลาง / ใหญ่)</th><th>ระยะเวลา</th><th>สถานะ</th><th>การจัดการ</th></tr></thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr><td>{{ $service->service_id }}</td><td><strong>{{ $service->service_name }}</strong></td><td class="service-description-cell">{{ $service->service_description }}</td>
                            <td><div class="service-price-list"><span>S ฿{{ number_format((float) $service->price_small, 0) }}</span><span>M ฿{{ number_format((float) $service->price_medium, 0) }}</span><span>L ฿{{ number_format((float) $service->price_large, 0) }}</span></div></td>
                            <td>{{ $service->service_duration_minutes }} นาที</td><td><span class="service-status {{ $service->service_status }}">{{ $service->service_status === 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</span></td>
                            <td><div class="service-actions"><a class="service-button" href="{{ route('admin.services.edit', $service->service_id) }}">แก้ไข</a><form method="POST" action="{{ route('admin.services.destroy', $service->service_id) }}" onsubmit="return confirm('ยืนยันนำบริการนี้ออกจากรายการหรือไม่?')">@csrf @method('DELETE')<button class="service-button danger" type="submit">ลบ</button></form></div></td></tr>
                    @empty
                        <tr><td class="booking-empty" colspan="7">ยังไม่มีบริการ กรุณาเพิ่มบริการรายการแรก</td></tr>
                    @endforelse
                </tbody>
            </table></div>
        </section>
    </main>
</body>
</html>
