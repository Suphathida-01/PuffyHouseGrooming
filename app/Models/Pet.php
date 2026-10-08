<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use SoftDeletes;

    protected $table = 'pets';
    // The migration uses Laravel's default primary key: id.

    protected $fillable = [
        'pet_name', 'pet_birth', 'pet_gender', 'pet_weight',
        'pet_notes', 'pet_image', 'customer_id', 'pet_breed_id',
    ];

    protected $casts = [
        'pet_birth' => 'date',
        'pet_weight' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function breed()
    {
        return $this->belongsTo(PetBreed::class, 'pet_breed_id', 'pet_breed_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'pet_id', 'id');
    }
}
