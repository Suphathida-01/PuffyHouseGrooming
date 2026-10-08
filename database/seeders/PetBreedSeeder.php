<?php

namespace Database\Seeders;

use App\Models\PetBreed;
use App\Models\PetType;
use Illuminate\Database\Seeder;

class PetBreedSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'สุนัข' => ['ชิวาวา', 'ชิบะอินุ', 'ชิสุ', 'ปอมเมอเรเนียน', 'พันธุ์ผสม'],
            'แมว' => ['เปอร์เซีย', 'สก็อตติชโฟลด์', 'แมวไทย', 'พันธุ์ผสม'],
        ];

        foreach ($types as $typeName => $breedNames) {
            $type = PetType::firstOrCreate(['pet_type_name' => $typeName]);

            foreach ($breedNames as $breedName) {
                PetBreed::firstOrCreate([
                    'pet_breed_name' => $breedName,
                    'pet_type_id' => $type->pet_type_id,
                ]);
            }
        }
    }
}
