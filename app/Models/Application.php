<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Attendance;
use App\Models\ApplicationBreak;

class Application extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'corrected_work_start',
        'corrected_work_end',
        'reason',
        'status',
        'applied_at',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => '承認待ち',
            self::STATUS_APPROVED  => '承認済み'
        ][$this->status];
    }

    public function applicationBreaks():HasMany
    {
        return $this->hasMany(ApplicationBreak::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
