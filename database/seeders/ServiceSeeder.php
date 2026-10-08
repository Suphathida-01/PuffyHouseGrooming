<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'service_name' => 'อาบน้ำและเป่าขน',
                'service_description' => 'ทำความสะอาดด้วยแชมพูสูตรอ่อนโยน พร้อมเป่าขนให้แห้งและจัดทรงเบื้องต้น',
                'service_duration_minutes' => 60,
                'price_small' => 300,
                'price_medium' => 400,
                'price_large' => 500,
                'service_status' => 'active',
            ],
            [
                'service_name' => 'อาบน้ำและตัดแต่งขน',
                'service_description' => 'อาบน้ำ เป่าขน และตัดแต่งทรงให้เหมาะกับน้องแต่ละตัว',
                'service_duration_minutes' => 120,
                'price_small' => 700,
                'price_medium' => 900,
                'price_large' => 1200,
                'service_status' => 'active',
            ],
            [
                'service_name' => 'ตัดเล็บและทำความสะอาดหู',
                'service_description' => 'ดูแลเล็บและทำความสะอาดหูอย่างอ่อนโยน เพื่อสุขอนามัยที่ดีของน้อง',
                'service_duration_minutes' => 60,
                'price_small' => 100,
                'price_medium' => 150,
                'price_large' => 200,
                'service_status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['service_name' => $service['service_name']],
                $service
            );
        }
    }
}
