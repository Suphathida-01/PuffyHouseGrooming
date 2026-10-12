@php
    $booking = $payment->booking;
    $reference = $payment->transaction_ref ?: 'TXN-' . str_pad((string) $payment->payment_id, 5, '0', STR_PAD_LEFT);
    $statusLabels = ['completed' => 'ชำระแล้ว', 'pending' => 'รอชำระ', 'failed' => 'ไม่สำเร็จ'];
    $statusClasses = ['completed' => 'status-paid', 'pending' => 'status-pending', 'failed' => 'status-refunded'];
    $methodLabels = ['qrCode' => 'PromptPay', 'creditCard' => 'บัตรเครดิต', 'bankTransfer' => 'โอนผ่านธนาคาร'];
    $bookingStatusLabels = ['pending' => 'รอดำเนินการ', 'completed' => 'เสร็จสิ้น', 'cancelled' => 'ยกเลิก'];
    $closeUrl = route('superadmin.payments', ['date' => $selectedDate, 'status' => $selectedStatus]);
@endphp

<dialog class="payment-detail-dialog" aria-labelledby="payment-dialog-title" data-close-url="{{ $closeUrl }}">
    <div class="payment-detail-dialog-content">
        <header class="payment-detail-dialog-heading">
            <div><h2 id="payment-dialog-title">รายละเอียดการชำระเงิน</h2><p>หมายเลขรายการ {{ $reference }}</p></div>
            <a class="schedule-close" href="{{ $closeUrl }}" aria-label="ปิด">×</a>
        </header>
        <div class="payment-detail-card-heading">
            <span class="status-badge {{ $statusClasses[$payment->payment_status] ?? 'status-refunded' }}">{{ $statusLabels[$payment->payment_status] ?? $payment->payment_status }}</span>
        </div>
        <div class="payment-detail-amount"><span>จำนวนเงิน</span><strong>฿{{ number_format((float) $payment->payment_amount, 2) }}</strong></div>
        <div class="payment-detail-information-grid">
            <section class="payment-detail-group" aria-labelledby="payment-info-title">
                <h3 id="payment-info-title">ข้อมูลการชำระเงิน</h3>
                <dl class="payment-detail-list">
                    <div><dt>ลูกค้า</dt><dd>{{ $booking?->customer?->user?->name ?? 'ไม่พบข้อมูลลูกค้า' }}</dd></div>
                    <div><dt>บริการ</dt><dd>{{ $booking?->service?->service_name ?? 'ไม่พบข้อมูลบริการ' }}</dd></div>
                    <div><dt>ช่องทางการชำระ</dt><dd>{{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}</dd></div>
                    <div><dt>วันเวลารับรายการ</dt><dd>{{ $payment->payment_date->format('d/m/Y · H:i') }}</dd></div>
                </dl>
            </section>
            <section class="payment-detail-group" aria-labelledby="booking-summary-title">
                <h3 id="booking-summary-title">ข้อมูลการจอง</h3>
                @if ($booking)
                    <dl class="payment-detail-list">
                        <div><dt>หมายเลขการจอง</dt><dd>PH-{{ str_pad((string) $booking->booking_id, 4, '0', STR_PAD_LEFT) }}</dd></div>
                        <div><dt>สัตว์เลี้ยง</dt><dd>{{ $booking->pet?->pet_name ?? 'ไม่พบข้อมูลสัตว์เลี้ยง' }}</dd></div>
                        <div><dt>วันเวลานัดหมาย</dt><dd>{{ $booking->booking_date->format('d/m/Y') }} · {{ substr($booking->booking_start_time, 0, 5) }}</dd></div>
                        <div><dt>สถานะการจอง</dt><dd>{{ $bookingStatusLabels[$booking->booking_status] ?? $booking->booking_status }}</dd></div>
                    </dl>
                @else
                    <p class="staff-empty">ไม่พบรายการจองที่เชื่อมกับการชำระเงินนี้</p>
                @endif
            </section>
        </div>
        @if ($payment->payment_status === 'pending')
            <footer class="schedule-dialog-actions">
                <form method="POST" action="{{ route('superadmin.payments.confirm', $payment->payment_id) }}">
                    @csrf
                    <button class="payment-confirm-button" type="submit">ยืนยันว่าชำระแล้ว</button>
                </form>
            </footer>
        @endif
    </div>
</dialog>

<script>
    const paymentDetailDialog = document.querySelector('.payment-detail-dialog');
    const paymentDetailCloseUrl = paymentDetailDialog.dataset.closeUrl;

    paymentDetailDialog.showModal();
    paymentDetailDialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        window.location.assign(paymentDetailCloseUrl);
    });
    paymentDetailDialog.addEventListener('click', (event) => {
        if (event.target === paymentDetailDialog) window.location.assign(paymentDetailCloseUrl);
    });
</script>
