<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSchedule extends Model
{
    protected $table = 'admin_schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = ['work_date', 'start_time', 'end_time', 'status', 'admin_id'];

    protected $casts = ['work_date' => 'date'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}
