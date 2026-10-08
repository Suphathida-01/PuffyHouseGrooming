<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service->exists ? 'แก้ไขบริการ' : 'เพิ่มบริการ' }} | Puffly House</title>
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
    <main class="admin-main service-form-main">
        <header class="admin-topbar service-page-header"><div><p class="admin-eyebrow">ตั้งค่าร้าน</p><h1>{{ $service->exists ? 'แก้ไขบริการ' : 'เพิ่มบริการ' }}</h1><p class="admin-subtitle">กรอกรายละเอียด ราคา และระยะเวลาให้บริการ</p></div></header>
        @if ($errors->any())<div class="booking-notice error">{{ $errors->first() }}</div>@endif
        <section class="admin-panel service-form-panel">
            <form class="service-form" method="POST" action="{{ $service->exists ? route('admin.services.update', $service->service_id) : route('admin.services.store') }}">
                @csrf @if ($service->exists) @method('PUT') @endif
                <label class="service-field service-field-full"><span>ชื่อบริการ</span><input type="text" name="service_name" maxlength="255" value="{{ old('service_name', $service->service_name) }}" required></label>
                <label class="service-field service-field-full"><span>รายละเอียด</span><textarea name="service_description" rows="4" maxlength="5000" required>{{ old('service_description', $service->service_description) }}</textarea></label>
                <label class="service-field service-field-half"><span>ระยะเวลา (นาที)</span><input type="number" name="service_duration_minutes" min="1" max="1440" step="1" value="{{ old('service_duration_minutes', $service->service_duration_minutes) }}" required></label>
                <label class="service-field service-field-half"><span>สถานะ</span><select name="service_status" required><option value="active" @selected(old('service_status', $service->service_status ?: 'active') === 'active')>เปิดใช้งาน</option><option value="inactive" @selected(old('service_status', $service->service_status) === 'inactive')>ปิดใช้งาน</option></select></label>
                <label class="service-field service-field-third"><span>ราคาไซซ์เล็ก (บาท)</span><input type="number" name="price_small" min="0" max="99999999.99" step="0.01" value="{{ old('price_small', $service->price_small) }}" required></label>
                <label class="service-field service-field-third"><span>ราคาไซซ์กลาง (บาท)</span><input type="number" name="price_medium" min="0" max="99999999.99" step="0.01" value="{{ old('price_medium', $service->price_medium) }}" required></label>
                <label class="service-field service-field-third"><span>ราคาไซซ์ใหญ่ (บาท)</span><input type="number" name="price_large" min="0" max="99999999.99" step="0.01" value="{{ old('price_large', $service->price_large) }}" required></label>
                <div class="service-form-actions service-field-full"><a class="service-button" href="{{ route('admin.services.index') }}">ยกเลิก</a><button class="service-button primary" type="submit">บันทึกบริการ</button></div>
            </form>
        </section>
    </main>
</body>
</html>
