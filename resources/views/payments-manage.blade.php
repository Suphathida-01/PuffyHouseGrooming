<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการการชำระเงิน | Puffly House</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v=22">
</head>
<body class="admin-dashboard">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="{{ route('dashboard') }}"><span class="admin-brand-icon">🐾</span><span><strong>Puffly House</strong><small>BACK-OFFICE</small></span></a>
        <nav class="admin-menu" aria-label="เมนูผู้ดูแลระบบ">
            <a href="{{ route('dashboard') }}"><span>⌂</span>หน้าหลัก</a><a href="{{ route('admin.bookings.index') }}"><span>▣</span>จัดการการจอง</a><a href="{{ route('admin.customers.index') }}"><span>♧</span>จัดการลูกค้า</a><a href="{{ route('admin.services.index') }}"><span>✂</span>บริการ &amp; แพ็กเกจ</a><a href="{{ route('staff.index') }}"><span>♧</span>จัดการช่าง</a><a class="active" href="{{ route('admin.payments') }}"><span>▥</span>จัดการชำระเงิน</a>
        </nav>
        <div class="admin-profile"><span class="admin-avatar">ADM</span><span><strong>{{ auth()->user()->name }}</strong><small>ผู้ดูแลระบบ</small></span><form method="POST" action="{{ route('logout') }}">@csrf<button title="ออกจากระบบ" aria-label="ออกจากระบบ">⇥</button></form></div>
    </aside>
    <main class="admin-main">
<div class="payments-page">
    <header class="page-header">
        <div class="page-title">
            <h1>จัดการการชำระเงิน</h1>
            <p>ติดตามประวัติการรับชำระเงินและตรวจสอบรายได้ประจำวัน</p>
        </div>
        @php $statusLabels = ['all' => 'สถานะทั้งหมด', 'completed' => 'ชำระแล้ว', 'pending' => 'รอชำระ', 'failed' => 'ไม่สำเร็จ']; @endphp
        <form class="payment-filter-actions report-filters" method="GET" action="{{ route('admin.payments') }}" aria-label="ตัวกรองรายการชำระเงิน">
            <label class="filter-control date-control">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                <input id="report-date" name="date" type="date" value="{{ $selectedDate }}" aria-label="เลือกวันที่">
            </label>
            <label class="payment-status-dropdown">
                <select name="status" aria-label="เลือกสถานะ">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="payment-status-chevron" aria-hidden="true"></span>
            </label>
            <button class="payment-filter-submit" type="submit">ค้นหา</button>
        </form>
    </header>

    @if (session('success'))<div class="booking-notice success">{{ session('success') }}</div>@endif
    @if (session('payment_error'))<div class="booking-notice error">{{ session('payment_error') }}</div>@endif

    <section class="summary-grid" aria-label="สรุปยอดการชำระเงิน">
        <article class="summary-card">
            <span class="summary-icon icon-green" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h3"/></svg></span>
            <div><p>รายได้ตามวันที่เลือก</p><strong>{{ number_format($summary['dailyRevenue']) }} บาท</strong><small>{{ $summary['dailyCompleted'] }} รายการที่ชำระ</small></div>
        </article>
        <article class="summary-card">
            <span class="summary-icon icon-amber" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v9l6 3M21 12a9 9 0 1 1-9-9"/></svg></span>
            <div><p>รอชำระทั้งหมด</p><strong>{{ $summary['pendingCount'] }} รายการ</strong><small>ยอดรวมค้างชำระ ฿{{ number_format($summary['pendingTotal']) }}</small></div>
        </article>
        <article class="summary-card">
            <span class="summary-icon icon-violet" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span>
            <div><p>ชำระสำเร็จตามวันที่เลือก</p><strong>{{ $summary['dailyCompleted'] }} รายการ</strong><small>นับเฉพาะรายการที่ชำระสำเร็จ</small></div>
        </article>
    </section>

    <section class="payments-table-section" aria-labelledby="payments-heading">
        <div class="table-heading">
            <div><h2 id="payments-heading">ประวัติรายการ</h2><p>รายการรับชำระเงินล่าสุด</p></div>
            <span class="result-count">{{ $payments->count() }} รายการ</span>
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
                            <td class="transaction-id">{{ $reference }}</td><td>{{ $customerName }}</td><td>{{ $serviceName }}</td><td class="amount">฿{{ number_format($payment->payment_amount) }}</td><td>{{ $methodLabel }}</td><td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td><td class="date-cell">{{ $paymentTime }}</td><td><div class="payment-table-actions"><a class="detail-button payment-open-page" href="{{ route('admin.payments.show', $payment->payment_id) }}">ดูรายละเอียด</a>@if ($payment->payment_status === 'pending')<form class="payment-confirm-form" method="POST" action="{{ route('admin.payments.confirm', $payment->payment_id) }}">@csrf<button class="payment-confirm-button" type="submit">ยืนยันชำระ</button></form>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">ไม่พบรายการชำระเงินในวันที่เลือก</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

    </main>
</body>
</html>
