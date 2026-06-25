<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BreakTime;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakTime>
 */
class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = BreakTIime::class;
    
    public function definition(): array
    {
        return [
            'attendance_id' => 1,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ];
    }
}
