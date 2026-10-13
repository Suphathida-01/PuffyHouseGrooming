<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการลูกค้า | Puffly House</title>
    <link rel="stylesheet"
        href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
</head>

<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main">
        <header class="admin-topbar customer-page-heading">
            <div>
                <h1>จัดการลูกค้า</h1>
                <p class="admin-subtitle">ฐานข้อมูลลูกค้าและประวัติการใช้บริการ</p>
            </div>
            <div class="customer-tools">
                <form class="customer-search" method="GET" action="{{ route('admin_customers_index') }}"><input
                        id="customer-search" type="search" name="search" value="{{ $search }}" autocomplete="off"
                        placeholder="พิมพ์ชื่อ อีเมล หรือเบอร์โทร" aria-label="ค้นหาตามชื่อ อีเมล หรือเบอร์โทร"><button
                        type="submit" aria-label="ค้นหา">⌕</button></form>
            </div>
        </header>
        @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
        <section class="admin-panel booking-table-panel">
            <div class="booking-table-wrap">
                <table class="booking-table customer-table">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>สัตว์เลี้ยง (จำนวน)</th>
                            <th>วันที่สมัคร</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $index => $customer)
                        @php $thaiMonths = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.',
                        'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.']; $registered = strtotime($customer->created_at); @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $customer->user->name }}</strong></td>
                            <td>{{ $customer->user->email }}</td>
                            <td>{{ $customer->user->user_phone ?: '—' }}</td>
                            <td><span class="customer-pet-count">{{ $customer->pet_count }} ตัว</span></td>
                            <td>{{ date('d', $registered) }} {{ $thaiMonths[(int) date('n', $registered)] }}
                                {{ date('Y', $registered) + 543 }}</td>
                            <td>
                                <div class="booking-actions"><a class="booking-action"
                                        href="{{ route('admin_customers_show', $customer->customer_id) }}">ดู</a><a
                                        class="booking-action outline"
                                        href="{{ route('admin_customers_edit', $customer->customer_id) }}">แก้ไข</a>
                                    <form method="POST"
                                        action="{{ route('admin_customers_destroy', $customer->customer_id) }}"
                                        onsubmit="return confirm('นำข้อมูลลูกค้ารายนี้ออกจากรายการหรือไม่?')">@csrf
                                        @method('DELETE')<button class="booking-action danger" type="submit">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="booking-empty" colspan="7">
                                {{ $search !== '' ? 'ไม่พบลูกค้าที่ตรงกับคำค้นหา' : 'ยังไม่มีข้อมูลลูกค้า' }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
    const customerSearchForm = document.querySelector('.customer-search');
    const customerSearchInput = document.getElementById('customer-search');
    let customerTableBody = document.querySelector('.customer-table tbody');
    let customerSearchTimer;
    let customerSearchRequest;

    async function refreshCustomerResults() {
        const searchUrl = new URL(customerSearchForm.action, window.location.origin);
        const searchTerm = customerSearchInput.value.trim();
        if (searchTerm) searchUrl.searchParams.set('search', searchTerm);
        else searchUrl.searchParams.delete('search');

        customerSearchRequest?.abort();
        customerSearchRequest = new AbortController();

        try {
            const response = await fetch(searchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: customerSearchRequest.signal,
            });
            if (!response.ok) return;

            const responseDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
            const updatedBody = responseDocument.querySelector('.customer-table tbody');
            if (!updatedBody) return;

            customerTableBody.replaceWith(updatedBody);
            customerTableBody = updatedBody;
            window.history.replaceState({}, '', searchUrl);
        } catch (error) {
            if (error.name !== 'AbortError') console.error('ค้นหารายชื่อลูกค้าไม่สำเร็จ', error);
        }
    }

    customerSearchInput.addEventListener('input', () => {
        clearTimeout(customerSearchTimer);
        customerSearchTimer = setTimeout(refreshCustomerResults, 250);
    });

    customerSearchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(customerSearchTimer);
        refreshCustomerResults();
    });
    </script>
</body>

</html>