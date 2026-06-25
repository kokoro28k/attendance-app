<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceListUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_users_all_attendance_items_for_multiple_days_are_displayed_on_attendance_list_page()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // 1日目のデータ
        $attendance1 = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-18',
            'work_start' => Carbon::create(2026, 6, 18, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 18, 18, 0, 0),
        ]);
        $attendance1->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 6, 18, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 18, 13, 0, 0),
        ]);

        // 2日目のデータ
        $attendance2 = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-19',
            'work_start' => Carbon::create(2026, 6, 19, 8, 30, 0),
            'work_end' => Carbon::create(2026, 6, 19, 17, 30, 0),
        ]);
        $attendance2->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 6, 19, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 19, 13, 30, 0),
        ]);

        Attendance::where('user_id', $user->id)  
            ->where('date', 'like', '2026-06-18%')
            ->update(['date' => '2026-06-18']);
        Attendance::where('user_id', $user->id)  
            ->where('date', 'like', '2026-06-19%')
            ->update(['date' => '2026-06-19']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-06',
        ]));

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            '06/18(木)',
            '09:00',
            '18:00',
            '1:00',
            '8:00',
        ]);

        $response->assertSeeInOrder([
            '06/19(金)',
            '08:30',
            '17:30',
            '1:30',
            '7:30',
        ]);
    }

    public function test_current_month_is_displayed_on_attendance_list_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-06',
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/06');

        Carbon::setTestNow();
    }

    public function test_attendance_items_of_previous_month_are_displayed_when_previous_month_button_is_pressed()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-05-15',
            'work_start' => Carbon::create(2026, 5, 15, 9, 0, 0),
            'work_end' => Carbon::create(2026, 5, 15, 18, 0, 0),
        ]);
        $attendance->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 5, 15, 12, 0, 0),
            'break_end' => Carbon::create(2026, 5, 15, 13, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
            ->where('date', 'like', '2026-05-15%')
            ->update(['date' => '2026-05-15']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-05',
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/05');
        $response->assertSeeInOrder([
            '05/15(金)',
            '09:00',
            '18:00',
            '1:00',
            '8:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_attendance_items_of_next_month_are_displayed_when_next_month_button_is_pressed()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-19',
            'work_start' => Carbon::create(2026, 6, 19, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 19, 18, 0, 0),
        ]);
       
        Attendance::where('user_id', $user->id)
        ->where('date', 'like', '2026-06-19%')
        ->update(['date' => '2026-06-19']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.index', [
            'year_month' => '2026-07',
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/07');
        $response->assertSee('07/01(水)');

        Carbon::setTestNow();
    }

    public function test_user_can_navigate_to_attendance_detail_page_by_clicking_detail_button()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-19',
            'work_start' => Carbon::create(2026, 6, 19, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 19, 18, 0, 0),
        ]);
        $attendance->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 6, 19, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 19, 13, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
            ->where('date', 'like', '2026-06-19%')
            ->update(['date' => '2026-06-19']);

        $this->actingAs($user);

        $response = $this->get(route('user.attendance.show', [
            'id' => $attendance->id,
        ]));

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $user->name,
            '2026年',
            '6月19日',
            '09:00',
            '18:00',
            '12:00',
            '13:00',
        ]);

        Carbon::setTestNow();
    }
}    
