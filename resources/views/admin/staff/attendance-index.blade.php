@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/attendance-index.css') }}">
@endsection

@section('content')
    <div class="staff-attendance">
        <div class="staff-attendance__inner">
            <h1 class="index-heading">{{ $user->name }}さんの勤怠</h1>
            {{-- 前月・翌月ナビゲーション --}}
            <div class="month-navigation">
                <div class="month-navigation__before">
                    <span class="month-navigation__arrow">←</span>
                    <a href="{{ route('staff.attendance', ['id' => $user->id, 'month' => $prevMonth]) }}"
                        class="month-button">前月
                    </a>
                </div>

                <div class="month-navigation__calender">
                    <img src="{{ asset('images/カレンダーアイコン8.png') }}" class="month-navigation__icon" alt="カレンダー">
                    <span class="current-month">
                        {{ $targetDate->format('Y/m') }}
                    </span>
                </div>

                <div class="month-navigation__after">
                    <span class="month-navigation__arrow">→</span>
                    <a href="{{ route('staff.attendance', ['id' => $user->id, 'month' => $nextMonth]) }}"
                        class="month-button">翌月
                    </a>

                </div>
            </div>


            {{-- 勤怠一覧テーブル --}}
            <table class="attendace-table">
                <tr class="attenfdance-row">
                    <th class="attendance-label">日付</th>
                    <th class="attendance-label">出勤</th>
                    <th class="attendance-label">退勤</th>
                    <th class="attendance-label">休憩</th>
                    <th class="attendance-label">合計</th>
                    <th class="attendance-label">詳細</th>
                </tr>

                @foreach ($attendances as $attendance)
                    <tr class="attendance-row">
                        <td class="attendance-data">{{ $attendance->date->format('m/d(D)') }}</td>
                        <td class="attendance-data">{{ $attendance->work_start }}</td>
                        <td class="attendance-data">{{ $attendance->work_end }}</td>
                        <td class="attendance-data">{{ $attendance->break_total_hm }}</td>
                        <td class="attendance-data">{{ $attendance->work_minutes }}</td>
                        <td class="attendance-data__detail">
                            <a class="detail" href="{{ route('staff.attendance') }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
