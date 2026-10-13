<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการการจอง | Puffly House</title>
    <link rel="stylesheet"
        href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/date-picker.css') }}?v={{ filemtime(public_path('css/date-picker.css')) }}">
</head>

<body class="admin-dashboard">
    @include('partials.admin-sidebar')

    <main class="admin-main">
        <header class="admin-topbar booking-page-heading">
            <div>
                <h1>จัดการการจอง</h1>
                <p class="admin-subtitle">ตรวจสอบและอัปเดตสถานะการนัดหมายอาบน้ำตัดขนสัตว์เลี้ยง</p>
            </div>
            <form class="booking-filters" method="GET" action="{{ route('admin_bookings_index') }}">
                <label class="filter-control date-control"><input type="date" name="date" value="{{ $date }}"
                        aria-label="กรองตามวันที่จอง"></label>
                <label><select name="status" aria-label="กรองตามสถานะ">
                        <option value="all" @selected($status==='all' )>ทั้งหมด</option>
                        <option value="pending" @selected($status==='pending' )>รอดำเนินการ</option>
                        <option value="cancelled" @selected($status==='cancelled' )>ยกเลิก</option>
                        <option value="completed" @selected($status==='completed' )>เสร็จสิ้น</option>
                    </select></label>
            </form>
        </header>

        @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
        @if (session('booking_error'))<div class="booking-notice error">{{ session('booking_error') }}</div>@endif
        @if ($errors->any())<div class="booking-notice error">{{ $errors->first() }}</div>@endif

        <section class="admin-panel booking-table-panel">
            <div class="booking-table-wrap">
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>หมายเลขจอง</th>
                            <th>ลูกค้า</th>
                            <th>สัตว์เลี้ยง</th>
                            <th>บริการ</th>
                            <th>วันที่/เวลา เข้ารับบริการ</th>
                            <th>ช่าง</th>
                            <th>สถานะ</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                        @php
                        $labels = ['pending' => 'รอดำเนินการ', 'completed' => 'เสร็จสิ้น', 'cancelled' => 'ยกเลิก'];
                        $thaiMonths = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.',
                        'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                        $dateParts = explode('-', $booking->booking_date->format('Y-m-d'));
                        @endphp
                        <tr>
                            <td><strong>PH-{{ str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $booking->customer?->user?->name }}</td>
                            <td>{{ $booking->pet?->pet_name }}</td>
                            <td>{{ $booking->service?->service_name }}</td>
                            <td>{{ (int) $dateParts[2] }} {{ $thaiMonths[(int) $dateParts[1]] }}
                                {{ substr($booking->booking_date, 0, 4) + 543 }}
                                {{ substr($booking->booking_start_time, 0, 5) }}</td>
                            <td>{{ $booking->admin?->user?->name ?: 'ยังไม่ระบุ' }}</td>
                            <td><span
                                    class="booking-status {{ $booking->booking_status }}">{{ $labels[$booking->booking_status] }}</span>
                            </td>
                            <td>
                                <div class="booking-actions">
                                    @if ($booking->booking_status === 'pending')
                                    <form method="POST"
                                        action="{{ route('admin_bookings_status', $booking->booking_id) }}">@csrf
                                        @method('PUT')<input type="hidden" name="booking_status"
                                            value="completed"><button class="booking-action outline"
                                            type="submit">เสร็จสิ้น</button></form>
                                    <form method="POST"
                                        action="{{ route('admin_bookings_status', $booking->booking_id) }}">@csrf
                                        @method('PUT')<input type="hidden" name="booking_status"
                                            value="cancelled"><button class="booking-action danger"
                                            type="submit">ยกเลิก</button></form>
                                    @else
                                    <span class="booking-no-action">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="booking-empty" colspan="8">ไม่พบรายการจองตามเงื่อนไขที่เลือก</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
    const bookingFilters = document.querySelector('.booking-filters');
    bookingFilters.querySelectorAll('input[type="date"], select[name="status"]').forEach((filter) => {
        filter.addEventListener('change', () => bookingFilters.requestSubmit());
    });
    </script>
    <script src="{{ asset('js/date-picker.js') }}?v={{ filemtime(public_path('js/date-picker.js')) }}"></script>
</body>

</html>