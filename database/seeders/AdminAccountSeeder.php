<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminAccountSeeder extends Seeder
{
    public function run(): void
    {
        if (! $this->command) {
            throw new RuntimeException('กรุณารัน seeder ผ่าน php artisan db:seed');
        }

        $password = $this->command->secret('ตั้งรหัสผ่านสำหรับ admin1@gmail.com');
        if (! $password) {
            throw new RuntimeException('ต้องระบุรหัสผ่านเพื่อสร้างบัญชี admin');
        }

        $user = User::updateOrCreate(
            ['email' => 'admin1@gmail.com'],
            [
                'name' => 'admin1',
                'password' => Hash::make($password),
                'user_role' => 'admin',
            ]
        );

        Admin::firstOrCreate(['user_id' => $user->id]);
        $this->command->info('สร้างบัญชี admin1 และข้อมูลในตาราง admins แล้ว');

        $this->call(AdminScheduleSeeder::class);
    }
}
