@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendances/index.css') }}">
@endsection

@section('content')
    <div class="attendance-index">
        <div class="attendance-index__inner">
            <h1 class="index-heading">勤怠一覧</h1>
            {{-- 前月・翌月ナビゲーション --}}
            <div class="month-navigation">
                <div class="month-navigation__before">
                    <span class="month-navigation__arrow">←</span>
                    <a href="{{ route('user.attendance.index', ['month' => $prevMonth]) }}" class="month-button">前月
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
                    <a href="{{ route('user.attendance.index', ['month' => $nextMonth]) }}" class="month-button">翌月
                    </a>

                </div>
            </div>


            {{-- 勤怠一覧テーブル --}}
            <table class="attendance-table">
                <tr class="attendance-row">
                    <th class="attendance-label">日付</th>
                    <th class="attendance-label">出勤</th>
                    <th class="attendance-label">退勤</th>
                    <th class="attendance-label">休憩</th>
                    <th class="attendance-label">合計</th>
                    <th class="attendance-label">詳細</th>
                </tr>
                @foreach ($rows as $row)
                    <tr class="attendance-row">
                        <td class="attendance-data">
                            {{ \Carbon\Carbon::parse($row['date'])->isoFormat('MM/DD(dd)') }}</td>
                        <td class="attendance-data">
                            {{ optional($row['attendance'])->work_start ? $row['attendance']->work_start->format('H:i') : '' }}
                        </td>
                        <td class="attendance-data">
                            {{ optional($row['attendance'])->work_end ? $row['attendance']->work_end->format('H:i') : '' }}
                        </td>
                        <td class="attendance-data">
                            {{ $row['attendance']->break_total_hm }}
                        </td>
                        <td class="attendance-data">
                            {{ \Carbon\CarbonInterval::minutes($row['attendance']->work_minutes)->cascade()->format('%H:%I') }}
                        </td>
                        <td class="attendance-data__detail">
                            @if ($row['attendance'])
                                <a class="detail"
                                    href="{{ route('user.attendance.show', [
                                        'id' => $row['attendance']->id,
                                    ]) }}">詳細</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
