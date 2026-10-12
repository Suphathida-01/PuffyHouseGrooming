<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminSchedule;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\PetBreed;
use App\Models\PetType;
use App\Models\Service;
use App\Models\Superadmin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $cat = PetType::firstOrCreate(['pet_type_name' => 'แมว']);
        $breed = PetBreed::firstOrCreate([
            'pet_breed_name' => 'เปอร์เซีย',
            'pet_type_id' => $cat->pet_type_id,
        ]);

        $service = Service::firstOrCreate(
            ['service_name' => 'บริการตัวอย่างสำหรับทดสอบ'],
            [
                'service_description' => 'ข้อมูลตัวอย่างสำหรับทดลองระบบ สามารถแก้ไขหรือลบได้',
                'service_duration_minutes' => 60,
                'price_small' => 300,
                'price_medium' => 400,
                'price_large' => 500,
                'service_status' => 'active',
            ],
        );

        $admin = Admin::query()->first();
        if (! $admin) {
            $adminUser = User::firstOrCreate(
                ['email' => 'sample.staff@example.test'],
                [
                    'name' => 'ช่างตัวอย่าง',
                    'password' => Hash::make(Str::random(48)),
                    'user_role' => 'admin',
                ],
            );
            $admin = Admin::firstOrCreate(['user_id' => $adminUser->id]);
        }

        User::query()->where('user_role', 'superadmin')->each(function (User $user) {
            Superadmin::firstOrCreate(['user_id' => $user->id]);
        });

        $customerUser = User::firstOrCreate(
            ['email' => 'sample.customer@example.test'],
            [
                'name' => 'ลูกค้าตัวอย่าง',
                'user_phone' => '0800000000',
                'password' => Hash::make(Str::random(48)),
                'user_role' => 'customer',
            ],
        );
        $customer = Customer::firstOrCreate(['user_id' => $customerUser->id]);
        $pet = Pet::firstOrCreate(
            ['customer_id' => $customer->customer_id, 'pet_name' => 'น้องตัวอย่าง'],
            [
                'pet_birth' => today()->subYears(2)->toDateString(),
                'pet_gender' => 'female',
                'pet_weight' => 4.5,
                'pet_notes' => 'ข้อมูลตัวอย่างสำหรับทดลองระบบ',
                'pet_breed_id' => $breed->pet_breed_id,
            ],
        );

        $workDate = today()->addDays(2);
        AdminSchedule::firstOrCreate([
            'admin_id' => $admin->admin_id,
            'work_date' => $workDate->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ], ['status' => 'available']);

        $examples = [
            [
                'ref' => 'SAMPLE-BOOKING-PENDING',
                'date' => $workDate->toDateString(),
                'start' => '10:00:00',
                'status' => 'pending',
                'payment_status' => 'pending',
            ],
            [
                'ref' => 'SAMPLE-BOOKING-COMPLETED',
                'date' => today()->subDay()->toDateString(),
                'start' => '13:00:00',
                'status' => 'completed',
                'payment_status' => 'completed',
            ],
        ];

        foreach ($examples as $example) {
            $booking = Booking::firstOrCreate(
                ['booking_notes' => '['.$example['ref'].'] ข้อมูลตัวอย่าง'],
                [
                    'booking_date' => $example['date'],
                    'booking_start_time' => $example['start'],
                    'booking_end_time' => date('H:i:s', strtotime($example['start'].' + 1 hour')),
                    'booking_total_price' => $service->price_small,
                    'booking_status' => $example['status'],
                    'customer_id' => $customer->customer_id,
                    'admin_id' => $admin->admin_id,
                    'service_id' => $service->service_id,
                    'pet_id' => $pet->id,
                ],
            );

            Payment::firstOrCreate(
                ['transaction_ref' => $example['ref']],
                [
                    'payment_date' => $example['payment_status'] === 'completed' ? $example['date'].' 14:00:00' : now(),
                    'payment_amount' => $service->price_small,
                    'payment_method' => 'qrCode',
                    'payment_status' => $example['payment_status'],
                    'booking_id' => $booking->booking_id,
                ],
            );
        }

        $this->command?->info('เพิ่มข้อมูลตัวอย่างแล้ว: ลูกค้า 1 ราย, สัตว์เลี้ยง 1 ตัว, บริการ 1 รายการ, การจองและการชำระเงิน 2 รายการ');
    }
}
