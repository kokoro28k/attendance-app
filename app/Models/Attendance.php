<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BreakTime;
use App\Models\Application;
use App\Models\User;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBreakTotalMinutesAttribute()
    {  
        $totalMinutes = 0;

        foreach ($this->breakTimes as $break) {
            if ($break->break_start && $break->break_end) {
            $totalMinutes += \Carbon\Carbon::parse($break->break_start)
                ->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
            }
        }

        return $totalMinutes;
    }

    public function getBreakTotalHmAttribute()
    {
        $totalMinutes = $this->break_total_minutes;

        return sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);
    }

    public function getWorkMinutesAttribute()
    {
        if (!$this->work_start || !$this->work_end) {
        return 0;
        }

        $workMinutes = \Carbon\Carbon::parse($this->work_start)
        ->diffInMinutes(\Carbon\Carbon::parse($this->work_end));

        return $workMinutes - $this->break_total_minutes;
    }
}
