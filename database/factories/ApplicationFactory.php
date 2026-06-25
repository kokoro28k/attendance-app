<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Application;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'corrected_work_start' => null,
            'corrected_work_end' => null,
            'reason' => $this->faker->realText(20),
            'status' => Application::STATUS_PENDING,
            'applied_at' => now(),
        ];
    }
}
