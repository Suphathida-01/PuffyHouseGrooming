<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'payment_date', 'payment_amount', 'payment_method',
        'transaction_ref', 'payment_status', 'booking_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
