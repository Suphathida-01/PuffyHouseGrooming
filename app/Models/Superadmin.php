<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Superadmin extends Model
{
    use SoftDeletes;

    protected $table = 'superadmins';
    protected $primaryKey = 'superadmin_id';

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
