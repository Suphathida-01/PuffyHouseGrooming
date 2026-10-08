<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetBreed extends Model
{
    protected $table = 'pet_breeds';
    protected $primaryKey = 'pet_breed_id';

    protected $fillable = ['pet_breed_name', 'pet_type_id'];

    public function petType()
    {
        return $this->belongsTo(PetType::class, 'pet_type_id', 'pet_type_id');
    }

    public function pets()
    {
        return $this->hasMany(Pet::class, 'pet_breed_id', 'pet_breed_id');
    }
}
