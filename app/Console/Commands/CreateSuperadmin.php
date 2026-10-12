<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateSuperadmin extends Command
{
    protected $signature = 'app:create-superadmin';

    protected $description = 'Create the owner Superadmin account';

    public function handle(): int
    {
        $name = trim((string) $this->ask('ชื่อผู้ใช้', 'SuperAdmin'));
        $email = trim((string) $this->ask('อีเมล', 'SuperAdmin1234@gmail.com'));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = $this->secret('กำหนดรหัสผ่าน (อย่างน้อย 8 ตัวอักษร)');
        $confirmation = $this->secret('ยืนยันรหัสผ่าน');

        if (! is_string($password) || strlen($password) < 8) {
            $this->error('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');

            return self::FAILURE;
        }

        if (! is_string($confirmation) || ! hash_equals($password, $confirmation)) {
            $this->error('รหัสผ่านที่ยืนยันไม่ตรงกัน');

            return self::FAILURE;
        }

        DB::transaction(function () use ($name, $email, $password): void {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'user_role' => 'superadmin',
            ]);

            $user->superadmin()->create();
        });

        $this->info("สร้างบัญชี Superadmin {$email} แล้ว");

        return self::SUCCESS;
    }
}
