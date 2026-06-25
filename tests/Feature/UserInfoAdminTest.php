<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class UserInfoAdminTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_admin_can_confirm_all_staff_name_and_email()
    {
        
        $user1 = User::factory()->create([
            'role' => 'user',
            'name' => 'テスト太郎',
            'email' => 'test1@example.com',
        ]);
        $user2 = User::factory()->create([
            'role' => 'user',
            'name' => 'テスト花子',
            'email' => 'test2@example.com',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('staff.index'));

        $response->assertSee('テスト太郎');
        $response->assertSee('test1@example.com');

        $response->assertSee('テスト花子');
        $response->assertSee('test2@example.com');
    }

    public function test_admin_can_confirm_selected_staff_attendance_list()
    {
        $fixedDateTime = Carbon::create(2026, 7, 1, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);
        
        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);


        for ($d = 1; $d <= 30; $d++) {
        $dateStr = "2026-06-" . str_pad($d, 2, '0', STR_PAD_LEFT);
        
            if ($d === 19) {
                $attendance = Attendance::factory()->create([
                    'user_id' => $user->id, 
                    'date' => $dateStr,
                    'work_start' => '08:30',
                    'work_end' => '17:30',
                    'status' => Attendance::STATUS_WORKING,
                ]);
                $attendance->breakTimes()->create([
                    'break_start' => '12:00',
                    'break_end' => '13:30',
                ]);
            } else {
                Attendance::factory()->create([
                    'user_id' => $user->id,
                    'date' => $dateStr,
                    'work_start' => '00:00', 
                    'work_end' => '00:00',
                    'status' => Attendance::STATUS_OFF,
                ]);
            }
        }

        \DB::table('attendances')->where('user_id', $user->id)->update([
            'date' => \DB::raw("strftime('%Y-%m-%d',date)")
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('staff.attendance', [
            'id' => $user->id,
            'year_month' => '2026-06',
        ]));

        $response->assertStatus(200);
     
        $response->assertSeeInOrder([
           '06/19(金)',
           '08:30',
           '17:30',
           '1:30', 
           '7:30',
        ]);
        
        Carbon::setTestNow();
    }

    public function test_attendance_items_of_previous_month_are_displayed_when_previous_month_button_is_pressed_on_staff_attendance_list_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        for ($d = 1; $d <= 31; $d++) {
            if ($d === 15) {
                $attendance = Attendance::factory()->create([
                    'user_id' => $user->id,
                    'date' => '2026-05-15',
                    'work_start' => '09:00',
                    'work_end' => '18:00',
                    'status' => Attendance::STATUS_FINISHED,
                ]);
                $attendance->breakTimes()->create([
                    'break_start' => '12:00',
                    'break_end'   => '13:00',
                ]);
            } else {
                Attendance::factory()->create([
                    'user_id' => $user->id,
                    'date' => "2026-05-" . str_pad($d, 2, '0', STR_PAD_LEFT),
                    'status'  => Attendance::STATUS_OFF,
                ]);
            }
        }
    
        \DB::table('attendances')->where('user_id', $user->id)->update([
            'date' => \DB::raw("strftime('%Y-%m-%d',date)")
        ]);

        $this->actingAs($admin,'admin');

        $response = $this->get(route('staff.attendance', [
            'id' => $user->id,
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

    public function test_attendance_items_of_next_month_are_displayed_when_next_month_button_is_pressed_on_staff_attendance_list_page()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        for ($d = 1; $d <= 31; $d++) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date' => "2026-07-" . str_pad($d, 2, '0', STR_PAD_LEFT),
                'status'  => Attendance::STATUS_OFF,
            ]);
        }
        
        \DB::table('attendances')->where('user_id', $user->id)->update([
            'date' => \DB::raw("strftime('%Y-%m-%d',date)")
        ]);
        
        $this->actingAs($admin,'admin');

        $response = $this->get(route('staff.attendance', [
            'year_month' => '2026-07',
            'id' => $user->id,
        ]));

        $response->assertStatus(200);

        $response->assertSee('2026/07');
        $response->assertSee('07/01(水)');

        Carbon::setTestNow();
    }

    public function test_admin_can_navigate_to_attendance_detail_page_by_clicking_detail_button()
    {
        $fixedDateTime = Carbon::create(2026, 6, 19, 9, 0, 0);
        Carbon::setTestNow($fixedDateTime);

        $user = User::factory()->create([
            'role' => 'user',
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        $response = $this->get(route('admin.attendance.show', [
            'id' => $attendance->id,
            'user_id' => $user->id,
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
