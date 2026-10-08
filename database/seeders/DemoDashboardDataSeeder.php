<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\PetBreed;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->first();
        $breed = PetBreed::query()->first();
        $services = Service::query()->orderBy('service_id')->get();

        if (! $admin || ! $breed || $services->isEmpty()) {
            $this->command?->warn('ข้ามข้อมูลจำลอง: ต้องมีแอดมิน สายพันธุ์สัตว์ และบริการอย่างน้อยหนึ่งรายการก่อน');

            return;
        }

        $user = User::firstOrCreate(
            ['email' => 'dashboard.demo@example.test'],
            [
                'name' => 'ลูกค้าทดลอง Dashboard',
                'password' => Hash::make(Str::random(48)),
                'user_role' => 'customer',
            ],
        );
        $customer = Customer::firstOrCreate(['user_id' => $user->id]);
        $pet = Pet::firstOrCreate(
            ['customer_id' => $customer->customer_id, 'pet_name' => 'น้องฟู (ข้อมูลทดลอง)'],
            [
                'pet_birth' => today()->subYears(3),
                'pet_gender' => 'female',
                'pet_weight' => 4.5,
                'pet_notes' => 'ข้อมูลสัตว์เลี้ยงจำลองสำหรับ dashboard',
                'pet_breed_id' => $breed->pet_breed_id,
            ],
        );

        $examples = collect(range(5, 0))->map(fn (int $monthsAgo, int $index) => [
            'key' => 'DEMO-DASHBOARD-PAID-'.$monthsAgo,
            'date' => now()->startOfMonth()->subMonths($monthsAgo)->addDays(2),
            'status' => 'completed',
            'service' => $services[$index % $services->count()],
        ])->concat([
            ['key' => 'DEMO-DASHBOARD-PENDING', 'date' => today()->addDays(3), 'status' => 'pending', 'service' => $services[0]],
            ['key' => 'DEMO-DASHBOARD-CONFIRMED', 'date' => today()->addDays(5), 'status' => 'confirmed', 'service' => $services[min(1, $services->count() - 1)]],
            ['key' => 'DEMO-DASHBOARD-CANCELLED', 'date' => today()->subDay(), 'status' => 'cancelled', 'service' => $services[min(2, $services->count() - 1)]],
        ]);

        foreach ($examples as $index => $example) {
            $service = $example['service'];
            $booking = Booking::firstOrCreate(
                ['booking_notes' => '['.$example['key'].'] ข้อมูลจำลองสำหรับ dashboard'],
                [
                    'booking_date' => Carbon::parse($example['date'])->toDateString(),
                    'booking_start_time' => '10:00:00',
                    'booking_end_time' => '11:00:00',
                    'booking_total_price' => $service->price_small,
                    'booking_status' => $example['status'],
                    'customer_id' => $customer->customer_id,
                    'admin_id' => $admin->admin_id,
                    'service_id' => $service->service_id,
                    'pet_id' => $pet->id,
                ],
            );

            $paymentStatus = match ($example['status']) {
                'completed' => 'completed',
                'cancelled' => 'failed',
                default => 'pending',
            };
            $payment = Payment::firstOrCreate(
                ['transaction_ref' => $example['key']],
                [
                    'payment_date' => $paymentStatus === 'completed' ? Carbon::parse($example['date'])->setTime(12, 0) : now(),
                    'payment_amount' => $service->price_small,
                    'payment_method' => 'qrCode',
                    'payment_status' => $paymentStatus,
                    'booking_id' => $booking->booking_id,
                ],
            );

            if ($payment->booking_id !== $booking->booking_id) {
                $this->command?->warn('พบรายการตัวอย่างเดิมที่ผูกกับการจองอื่น ข้ามการแก้ไขข้อมูลนั้น');
            }
        }

        $this->command?->info('เพิ่มข้อมูลตัวอย่างสำหรับ dashboard แล้ว (ลูกค้าทดลอง 1 ราย, จอง 9 รายการ)');
    }
}
