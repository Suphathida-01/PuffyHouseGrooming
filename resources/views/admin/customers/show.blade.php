<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายละเอียดลูกค้า | Puffly House</title>
    <link rel="stylesheet"
        href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
</head>

<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="admin-eyebrow">ข้อมูลสมาชิก</p>
                <h1>รายละเอียดลูกค้า</h1>
                <p class="admin-subtitle">ข้อมูลบัญชี สัตว์เลี้ยง และประวัติการจอง</p>
            </div>
            <div class="customer-tools"><a class="customer-add-button customer-back-button"
                    href="{{ route('admin_customers_index') }}">กลับ</a><a class="customer-add-button"
                    href="{{ route('admin_customers_edit', $customer->customer_id) }}">แก้ไขข้อมูล</a></div>
        </header>
        <section class="admin-panel customer-details">
            <h2>{{ $customer->user->name }}</h2>
            <p><strong>อีเมล</strong>{{ $customer->user->email }}</p>
            <p><strong>เบอร์โทร</strong>{{ $customer->user->user_phone ?: '—' }}</p>
            <h3>สัตว์เลี้ยง ({{ $customer->pets->count() }})</h3>
            @forelse ($customer->pets as $pet)<p>
                <strong>{{ $pet->pet_name }}</strong>{{ $pet->breed->pet_breed_name ?? '' }}
                {{ $pet->breed->petType->pet_type_name ?? '' }}</p>@empty<p>ยังไม่มีข้อมูลสัตว์เลี้ยง</p>
            @endforelse
        </section>
        <section class="admin-panel">
            <div class="panel-heading">
                <div>
                    <h2>ประวัติการจอง</h2>
                    <p>รายการล่าสุดไม่เกิน 10 รายการ</p>
                </div>
            </div>
            <div class="booking-table-wrap">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>หมายเลขจอง</th>
                            <th>สัตว์เลี้ยง</th>
                            <th>บริการ</th>
                            <th>วันที่/เวลา</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                        @php $statusLabels = ['pending' => 'รอดำเนินการ', 'completed' => 'เสร็จสิ้น', 'cancelled' =>'ยกเลิก']; @endphp
                        <tr>
                            <td>PH-{{ str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $booking->pet?->pet_name }}</td>
                            <td>{{ $booking->service?->service_name }}</td>
                            <td>{{ $booking->booking_date->format('d/m/Y') }}
                                {{ substr($booking->booking_start_time, 0, 5)}}</td>
                            <td>{{ $statusLabels[$booking->booking_status] }}</td>
                        </tr>
                        @empty<tr>
                            <td class="booking-empty" colspan="5">ยังไม่มีประวัติการจอง</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>