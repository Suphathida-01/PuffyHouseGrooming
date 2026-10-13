<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการการชำระเงิน | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/date-picker.css') }}?v={{ filemtime(public_path('css/date-picker.css')) }}">
</head>
<body class="admin-dashboard">
    @include('partials.admin-sidebar')
    <main class="admin-main">
<div class="payments-page">
    <header class="page-header">
        <div class="page-title">
            <h1>จัดการการชำระเงิน</h1>
            <p>ติดตามรายการที่ต้องตรวจสอบและสถานะการชำระเงิน โดยกรองวันที่หรือสถานะได้</p>
        </div>
        @php
            $statusLabels = ['all' => 'สถานะทั้งหมด', 'completed' => 'ชำระแล้ว', 'pending' => 'รอชำระ', 'failed' => 'ไม่สำเร็จ'];
            $summaryScopeLabel = $selectedDate === '' ? 'รวมทุกวันที่มีรายการ' : 'วันที่ '.\Illuminate\Support\Carbon::parse($selectedDate)->format('d/m/Y');
        @endphp
        <form class="payment-filter-actions report-filters" method="GET" action="{{ route('superadmin_payments') }}" aria-label="กรองรายการตามวันที่" data-date-picker-auto-submit>
            <input type="hidden" name="status" value="{{ $selectedStatus }}">
            <label class="filter-control date-control">
                <input id="report-date" name="date" type="date" value="{{ $selectedDate }}" aria-label="เลือกวันที่">
            </label>
        </form>
    </header>

    @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
    @if (session('payment_error'))<div class="booking-notice error">{{ session('payment_error') }}</div>@endif

    <section class="summary-grid" aria-label="สรุปยอดการชำระเงิน">
        <article class="summary-card">
            <span class="summary-icon icon-amber" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v9l6 3M21 12a9 9 0 1 1-9-9"/></svg></span>
            <div><p>รอตรวจสอบ</p><strong>{{ $summary['pendingCount'] }} รายการ</strong><small>{{ $summaryScopeLabel }} · ยอดค้าง ฿{{ number_format($summary['pendingTotal']) }}</small></div>
        </article>
        <article class="summary-card">
            <span class="summary-icon icon-green" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span>
            <div><p>ชำระแล้ว</p><strong>{{ $summary['completedCount'] }} รายการ</strong><small>{{ $summaryScopeLabel }} · รวม ฿{{ number_format($summary['completedRevenue']) }}</small></div>
        </article>
        <article class="summary-card">
            <span class="summary-icon icon-red" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg></span>
            <div><p>ไม่สำเร็จ</p><strong>{{ $summary['failedCount'] }} รายการ</strong><small>{{ $summaryScopeLabel }} · รวม ฿{{ number_format($summary['failedTotal']) }}</small></div>
        </article>
    </section>

    <section class="payments-table-section" aria-labelledby="payments-heading">
        <div class="table-heading">
            <div><h2 id="payments-heading">ประวัติรายการ</h2><p>รายการชำระเงินตามตัวกรอง เรียงจากล่าสุด</p></div>
            <div class="payment-table-heading-actions">
                <form class="payment-status-filter" method="GET" action="{{ route('superadmin_payments') }}" aria-label="กรองประวัติตามสถานะ">
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                    <label class="payment-status-dropdown">
                        <select name="status" aria-label="เลือกสถานะ" onchange="this.form.submit()">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <span class="payment-status-chevron" aria-hidden="true"></span>
                    </label>
                </form>
                <span class="result-count">{{ $payments->count() }} รายการ</span>
            </div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead><tr>
                    <th>หมายเลขรายการ</th><th>ลูกค้า</th><th>บริการ</th><th>จำนวนเงิน</th>
                    <th>ช่องทางการชำระ</th><th>สถานะ</th><th>วัน-เวลาทำรายการ</th><th>การจัดการ</th>
                </tr></thead>
                <tbody>
                    @forelse ($payments as $payment)
                        @php
                            $reference = $payment->transaction_ref ?: 'TXN-' . str_pad((string) $payment->payment_id, 5, '0', STR_PAD_LEFT);
                            $customerName = $payment->booking?->customer?->user?->name ?? 'ไม่พบข้อมูลลูกค้า';
                            $serviceName = $payment->booking?->service?->service_name ?? 'ไม่พบข้อมูลบริการ';
                            $methodLabel = match ($payment->payment_method) {
                                'qrCode' => 'PromptPay',
                                'creditCard' => 'บัตรเครดิต',
                                'bankTransfer' => 'โอนผ่านธนาคาร',
                                default => $payment->payment_method,
                            };
                            [$statusLabel, $statusClass] = match ($payment->payment_status) {
                                'completed' => ['ชำระแล้ว', 'status-paid'],
                                'pending' => ['รอชำระ', 'status-pending'],
                                default => ['ไม่สำเร็จ', 'status-refunded'],
                            };
                            $paymentTime = \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d/m/Y · H:i');
                        @endphp
                        <tr>
                            <td class="transaction-id">{{ $reference }}</td><td>{{ $customerName }}</td><td>{{ $serviceName }}</td><td class="amount">฿{{ number_format($payment->payment_amount) }}</td><td>{{ $methodLabel }}</td><td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td><td class="date-cell">{{ $paymentTime }}</td><td><div class="payment-table-actions"><a class="detail-button payment-open-page" href="{{ route('superadmin_payments', ['date' => $selectedDate, 'status' => $selectedStatus, 'detail' => $payment->payment_id]) }}">ดูรายละเอียด</a>@if ($payment->payment_status === 'pending')<form class="payment-confirm-form" method="POST" action="{{ route('superadmin_payments_confirm', $payment->payment_id) }}">@csrf<button class="payment-confirm-button" type="submit">ยืนยันชำระ</button></form><form class="payment-confirm-form" method="POST" action="{{ route('superadmin_payments_reject', $payment->payment_id) }}" onsubmit="return confirm('ปฏิเสธรายการชำระเงินนี้หรือไม่? สถานะจะเปลี่ยนเป็นไม่สำเร็จ')">@csrf<button class="payment-reject-button" type="submit">ปฏิเสธรายการ</button></form>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">{{ $selectedDate === '' ? 'ไม่พบรายการชำระเงิน' : 'ไม่พบรายการชำระเงินในวันที่เลือก' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @if ($selectedPayment)
        @include('superadmin.payment-show', ['payment' => $selectedPayment])
    @endif
</div>

    </main>
<script src="{{ asset('js/date-picker.js') }}?v={{ filemtime(public_path('js/date-picker.js')) }}"></script>
</body>
</html>
