<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class AttendanceDateTimeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_attendance_page_displays_currecnt_datetime()
    {
        $fixedDate = Carbon::create(2026,6,19,10,00,00);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee($fixedDate->translatedFormat('Y年m月d日(D)'));
        $response->assertSee($fixedDate->format('H:i'));
    }
}
