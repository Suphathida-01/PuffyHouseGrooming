<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        if (! $admin) {
            $adminUser = User::where('user_role', 'admin')->first();

            if (! $adminUser) {
                $this->command?->warn('ไม่พบผู้ใช้ role admin ในตาราง users กรุณาสร้างบัญชี admin ก่อน');
                return;
            }

            $admin = Admin::firstOrCreate(['user_id' => $adminUser->id]);
        }

        $created = 0;

        for ($day = 0; $day < 7; $day++) {
            $scheduleDate = today()->addDays($day);
            if ($scheduleDate->isSaturday()) {
                continue;
            }

            $date = $scheduleDate->toDateString();
            $hasSchedule = AdminSchedule::where('admin_id', $admin->admin_id)
                ->whereDate('work_date', $date)
                ->exists();

            if (! $hasSchedule) {
                AdminSchedule::create([
                    'admin_id' => $admin->admin_id,
                    'work_date' => $date,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'status' => 'available',
                ]);
                $created++;
            }
        }

        $this->command?->info($created
            ? 'เพิ่มกะตัวอย่าง 09:00–17:00 จำนวน '.$created.' วันแล้ว (วันนี้ถึงอีก 6 วัน ไม่รวมวันเสาร์)'
            : 'มีตารางของ 7 วันถัดไปอยู่แล้ว จึงไม่เพิ่มข้อมูลซ้ำ');
    }
}
