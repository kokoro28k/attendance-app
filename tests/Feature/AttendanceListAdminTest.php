<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceListAdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_all_users_attendance_records_are_displayed_on_admin_attendance_list()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user1 = User::factory()->create([
            'role' => 'user',
        ]);
        $user2 = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

         // 1人目のデータ
        $attendance1 = Attendance::factory()->create([
            'user_id' => $user1->id,
            'date' => '2026-06-19',
            'work_start' => Carbon::create(2026, 6, 1, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 19, 18, 0, 0),
        ]);
        $attendance1->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 6, 19, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 19, 13, 0, 0),
        ]);

        // 2人目のデータ
        $attendance2 = Attendance::factory()->create([
            'user_id' => $user2->id,
            'date' => '2026-06-19',
            'work_start' => Carbon::create(2026, 6, 19, 8, 30, 0),
            'work_end' => Carbon::create(2026, 6, 19, 17, 30, 0),
        ]);
        $attendance2->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 6, 19, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 19, 13, 30, 0),
        ]);

        Attendance::where('user_id', $user1->id)  
            ->where('date', 'like', '2026-06-19%')
            ->update(['date' => '2026-06-19']);
        Attendance::where('user_id', $user2->id)  
            ->where('date', 'like', '2026-06-19%')
            ->update(['date' => '2026-06-19']);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.attendance.index', [
            'date' => '2026-06-19',
        ]));

        $response->assertStatus(200);

        $response->assertSee($user1->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('1:00');
        $response->assertSee('8:00');
        
        $response->assertSee($user2->name);
        $response->assertSee('08:30');
        $response->assertSee('17:30');
        $response->assertSee('1:30');
        $response->assertSee('7:30');
        
        Carbon::setTestNow();
    }

    public function test_current_day_is_displayed_on_attendance_list_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.attendance.index', [
            'date' => '2026-06-19',
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/06/19');

        Carbon::setTestNow();
    }

    public function test_attendance_items_of_previous_day_are_displayed_when_previous_day_button_is_pressed()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-18',
            'work_start' => Carbon::create(2026, 6, 18, 9, 0, 0),
            'work_end' => Carbon::create(2026, 6, 18, 18, 0, 0),
        ]);
        $attendance->breakTimes()->create([
            'break_start' =>  Carbon::create(2026, 6, 18, 12, 0, 0),
            'break_end' => Carbon::create(2026, 6, 18, 13, 0, 0),
        ]);

        Attendance::where('user_id', $user->id)
            ->where('date', 'like', '2026-06-18%')
            ->update(['date' => '2026-06-18']);


        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.attendance.index', [
            'date' => '2026-06-18',
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/06/18');
        $response->assertSeeInOrder([
            $user->name,
            '09:00',
            '18:00',
            '1:00',
            '8:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_attendance_items_of_next_day_are_displayed_when_next_day_button_is_pressed()
    {
        $fixedDateTime = Carbon::create(2026, 6, 18, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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


        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.attendance.index', [
            'date' => '2026-06-19',
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/06/19');
        $response->assertSeeInOrder([
            $user->name,
            '09:00',
            '18:00',
            '1:00',
            '8:00',
        ]);

        Carbon::setTestNow();
    }
}