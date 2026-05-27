@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/index.css') }}">
@endsection

@section('content')
    <div class="attendance-index">
        <div class="attendance-index__inner">
            <h1 class="index-heading"> {{ \Carbon\Carbon::parse($targetDate)->format('Y年n月j日の勤怠') }}</h1>
            {{-- 前日・翌日ナビゲーション --}}
            <div class="date-navigation">
                <div class="date-navigation__before">
                    <span class="date-navigation__arrow">←</span>
                    <a href="{{ route('admin.attendance.index', ['date' => $prevDate]) }}" class="date-button">前日
                    </a>
                </div>

                <div class="date-navigation__calender">
                    <img src="{{ asset('images/カレンダーアイコン8.png') }}" class="month-navigation__icon" alt="カレンダー">
                    <span class="current-month">
                        {{ \Carbon\Carbon::parse($targetDate)->format('Y/m/d') }}
                    </span>
                </div>

                <div class="date-navigation__after">
                    <span class="date-navigation__arrow">→</span>
                    <a href="{{ route('admin.attendance.index', ['date' => $nextDate]) }}" class="date-button">翌日
                    </a>

                </div>
            </div>


            {{-- 勤怠一覧テーブル --}}
            <table class="attendace-table">
                <tr class="attenfdance-row">
                    <th class="attendance-label">名前</th>
                    <th class="attendance-label">出勤</th>
                    <th class="attendance-label">退勤</th>
                    <th class="attendance-label">休憩</th>
                    <th class="attendance-label">合計</th>
                    <th class="attendance-label">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    <tr class="attendance-row">
                        <td class="attendance-data">{{ $attendance->user->name }}</td>
                        <td class="attendance-data">{{ $attendance->work_start }}</td>
                        <td class="attendance-data">{{ $attendance->work_end }}</td>
                        <td class="attendance-data">{{ $attendance->break_total_hm }}</td>
                        <td class="attendance-data">{{ $attendance->work_minutes }}</td>
                        <td class="attendance-data__detail">
                            <a class="detail" href="{{ route('admin.attendance.show') }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </table>

        </div>
    </div>
@endsection
