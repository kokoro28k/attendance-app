<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BreakTime;
use App\Models\Application;

class Attendance extends Model
{
    use HasFactory;

    const STATUS_OFF = 0;
    const STATUS_WORKING = 1;
    const STATUS_BREAK = 2;
    const STATUS_FINISHED = 3;
        
    protected $fillable = [
        'user_id',
        'date',
        'work_start',
        'work_end',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_OFF => '勤務外',
            self::STATUS_WORKING => '出勤中',
            self::STATUS_BREAK => '休憩中',
            self::STATUS_FINISHED => '退勤済',
        ][$this->status];
    }

    public function breakTimes(): HasMany
    {
        return $this->hasMany(BreakTime::class);
    }
    
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
