<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Application;

class ApplicationBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'corrected_break_start',
        'corrected_break_end',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

}
