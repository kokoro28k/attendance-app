<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ApplicationBreak;
use App\Models\Application;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationBreak>
 */
class ApplicationBreakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ApplicationBreak::class;
    
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'corrected_break_start' => null,
            'corrected_break_end' => null,
        ];
    }
}
