<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $customer ? 'แก้ไขข้อมูลลูกค้า' : 'เพิ่มลูกค้า' }} | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=9">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ"><a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a><a href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a><a class="active" href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a><a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a><a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a><a href="{{ route('admin.payments') }}"><span>▥</span>รายงานยอดขาย</a></nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>
    <main class="admin-main customer-form-main">
        <header class="admin-topbar"><div><p class="admin-eyebrow">ข้อมูลสมาชิก</p><h1>{{ $customer ? 'แก้ไขข้อมูลลูกค้า' : 'เพิ่มลูกค้า' }}</h1><p class="admin-subtitle">กรอกข้อมูลบัญชีลูกค้า</p></div></header>
        @if ($errors->any())<div class="booking-notice error">{{ $errors->first() }}</div>@endif
        <section class="admin-panel customer-form-panel"><form method="POST" action="{{ $customer ? route('admin.customers.update', $customer->customer_id) : route('admin.customers.store') }}">
            @csrf @if ($customer) @method('PUT') @endif
            <label>ชื่อ-นามสกุล<input type="text" name="name" value="{{ old('name', $customer ? $customer->user->name : '') }}" required maxlength="255"></label>
            <label>อีเมล<input type="email" name="email" value="{{ old('email', $customer ? $customer->user->email : '') }}" required maxlength="255"></label>
            <label>เบอร์โทรศัพท์<input type="text" name="user_phone" value="{{ old('user_phone', $customer ? $customer->user->user_phone : '') }}" maxlength="20"></label>
            @unless ($customer)<label>รหัสผ่านเริ่มต้น<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>อย่างน้อย 8 ตัวอักษร</small></label>@endunless
            <div class="customer-form-actions"><a class="booking-action" href="{{ route('admin.customers.index') }}">กลับ</a><button class="customer-add-button" type="submit">บันทึกข้อมูล</button></div>
        </form></section>
    </main>
</body>
</html>
