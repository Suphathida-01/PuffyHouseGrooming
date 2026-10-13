<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $customer ? 'แก้ไขข้อมูลลูกค้า' : 'เพิ่มลูกค้า' }} | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
</head>
<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main customer-form-main">
        <header class="admin-topbar"><div><p class="admin-eyebrow">ข้อมูลสมาชิก</p><h1>{{ $customer ? 'แก้ไขข้อมูลลูกค้า' : 'เพิ่มลูกค้า' }}</h1><p class="admin-subtitle">กรอกข้อมูลบัญชีลูกค้า</p></div></header>
        @if ($errors->any())<div class="booking-notice error">{{ $errors->first() }}</div>@endif
        <section class="admin-panel customer-form-panel"><form method="POST" action="{{ $customer ? route('superadmin_customers_update', $customer->customer_id) : route('superadmin_customers_store') }}">
            @csrf @if ($customer) @method('PUT') @endif
            <label>ชื่อ-นามสกุล<input type="text" name="name" value="{{ old('name', $customer ? $customer->user->name : '') }}" required maxlength="255"></label>
            <label>อีเมล<input type="email" name="email" value="{{ old('email', $customer ? $customer->user->email : '') }}" required maxlength="255"></label>
            <label>เบอร์โทรศัพท์<input type="text" name="user_phone" value="{{ old('user_phone', $customer ? $customer->user->user_phone : '') }}" maxlength="20"></label>
            @unless ($customer)<label>รหัสผ่านเริ่มต้น<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>อย่างน้อย 8 ตัวอักษร</small></label>@endunless
            <div class="customer-form-actions"><a class="customer-add-button customer-back-button" href="{{ route('superadmin_customers_index') }}">กลับ</a><button class="customer-add-button" type="submit">บันทึกข้อมูล</button></div>
        </form></section>
    </main>
</body>
</html>
