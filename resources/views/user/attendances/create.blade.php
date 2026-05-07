@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendance/create.css') }}">
@endsection

@section('content')
    <div class="attendance-create__inner">
        <div class="attendance-status">
            @if ($attendance->status === $STATUS_OFF)
                <span class="status">勤務外</span>
            @elseif ($attendance->status === $STATUS_WORKING)
                <span class="status">出勤中</span>
            @elseif ($attendance->status === $STATUS_BREAK)
                <span class="status">休憩中</span>
            @elseif ($attendance->status === $STATUS_FINISHED)
                <span class="status">退勤済</span>
        </div>

        
    @endsection
