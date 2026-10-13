<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการบริการ | Puffly House</title>
    <link rel="stylesheet"
        href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
</head>

<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main">
        <header class="admin-topbar service-page-header">
            <div>
                <h1>จัดการบริการ</h1>
                <p class="admin-subtitle">ตั้งค่าบริการ ราคาแยกตามขนาดสัตว์เลี้ยง และสถานะการเปิดให้บริการ</p>
            </div><a class="service-button primary" href="{{ route('superadmin_services_create') }}">＋ เพิ่มบริการ</a>
        </header>
        @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
        <section class="admin-panel booking-table-panel">
            <div class="booking-table-wrap">
                <table class="booking-table service-table">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อบริการ</th>
                            <th>รายละเอียด</th>
                            <th>ราคา (เล็ก / กลาง / ใหญ่)</th>
                            <th>ระยะเวลา</th>
                            <th>สถานะ</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                        <tr>
                            <td>{{ $service->service_id }}</td>
                            <td><strong>{{ $service->service_name }}</strong></td>
                            <td class="service-description-cell">{{ $service->service_description }}</td>
                            <td>
                                <div class="service-price-list"><span>S
                                        ฿{{ number_format((float) $service->price_small, 0) }}</span><span>M
                                        ฿{{ number_format((float) $service->price_medium, 0) }}</span><span>L
                                        ฿{{ number_format((float) $service->price_large, 0) }}</span></div>
                            </td>
                            <td>{{ $service->service_duration_minutes }} นาที</td>
                            <td><span
                                    class="service-status {{ $service->service_status }}">{{ $service->service_status === 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</span>
                            </td>
                            <td>
                                <div class="service-actions"><a class="service-button edit"
                                        href="{{ route('superadmin_services_edit', $service->service_id) }}">แก้ไข</a>
                                    <form method="POST"
                                        action="{{ route('superadmin_services_destroy', $service->service_id) }}"
                                        onsubmit="return confirm('ยืนยันนำบริการนี้ออกจากรายการหรือไม่?')">@csrf
                                        @method('DELETE')<button class="service-button danger" type="submit">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="booking-empty" colspan="7">ยังไม่มีบริการ กรุณาเพิ่มบริการรายการแรก</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>