<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'services';
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'service_name', 'service_description', 'service_duration_minutes',
        'price_small', 'price_medium', 'price_large', 'service_status', 'service_image_path',
    ];

    protected $casts = [
        'service_duration_minutes' => 'integer',
        'price_small' => 'decimal:2',
        'price_medium' => 'decimal:2',
        'price_large' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'service_id', 'service_id');
    }
}
