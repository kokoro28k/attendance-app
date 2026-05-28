@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendances/create.css') }}">
@endsection

@section('content')
    <div class="attendance-create">
        <div class="attendance-create__inner">
            <div class="attendance-status">
                @if (is_null($attendance) || $attendance->status === $STATUS_OFF)
                    <span class="status">勤務外</span>
                @elseif ($attendance && $attendance->status === $STATUS_WORKING)
                    <span class="status">出勤中</span>
                @elseif ($attendance && $attendance->status === $STATUS_BREAK)
                    <span class="status">休憩中</span>
                @elseif ($attendance && $attendance->status === $STATUS_FINISHED)
                    <span class="status">退勤済</span>
                @endif
            </div>

            <div class="attendance-datetime">
                <p class="attendance-date">{{ $formattedDate }}</p>
                <p class="attendance-time">{{ $formattedTime }}</p>
            </div>

            <div class="attendance-button">
                @if (is_null($attendance) || $attendance->status === $STATUS_OFF)
                    <form class="attendance-form__work_start" action="{{ route('user.attendance.start') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ $formattedDate }}">
                        <input type="hidden" name="work_start" value="{{ $formattedTime }}">
                        <button class="button button--work_start">出勤</button>
                    </form>
                @elseif ($attendance && $attendance->status === $STATUS_WORKING)
                    <div class="attendance-working">
                        <form class="attendance-form__work_end" action="{{ route('user.attendance.end') }}" method="POST">
                            @csrf
                            <input type="hidden" name="date" value="{{ $formattedDate }}">
                            <input type="hidden" name="work_end" value="{{ $formattedTime }}">
                            <button class="button button--work_end">退勤</button>
                        </form>

                        <form class="attendance-form__break_start" action="{{ route('user.break.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="date" value="{{ $formattedDate }}">
                            <input type="hidden" name="break_start" value="{{ $formattedTime }}">
                            <button class="button button--break_start">休憩入</button>
                        </form>
                    </div>
                @elseif ($attendance && $attendance->status === $STATUS_BREAK)
                    <form class="attendance-form__break_end" action="{{ route('user.break.end') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ $formattedDate }}">
                        <input type="hidden" name="break_end" value="{{ $formattedTime }}">
                        <button class="button button--break_end">休憩戻</button>
                    </form>
                @elseif ($attendance && $attendance->status === $STATUS_FINISHED)
                    <p class="work_end-message">お疲れ様でした。</p>
                @endif
            </div>
        </div>
    </div>
@endsection
