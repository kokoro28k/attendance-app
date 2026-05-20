@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/show.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        <div class="attendance-detail__inner">
            <h1 class="detail-heading">勤怠詳細</h1>
            <form action="{{ route('user.application.store') }}" method="post">
                @csrf
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

                <div class="table-wrapper">
                    <table class="detail-table">
                        <tr class="detail-row">
                            <th class="detail-label">名前</th>
                            <td class="detail-data">{{ $attendance->user->name }}</td>
                        </tr>
                        <tr class="detail-row">
                            <th class="detail-label">日付</th>
                            <td class="detail-data">
                                <div class="data-row">
                                    <span class="date-row__year">{{ $year }}</span>
                                    <span class="date-row__day">
                                        {{ $monthDay }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="detail-row">
                            <th class="detail-label">出勤・退勤</th>
                            <td class="detail-data">
                                <div class="attendance-time">
                                    <input type="time" name="work_start"
                                        value="{{ old('work_start', optional($attendance->work_start)->format('H:i')) }}">
                                    <span class="time-separator">～</span>
                                    <input type="time" name="work_end"
                                        value="{{ old('work_end', optional($attendance->work_end)->format('H:i')) }}">
                                </div>
                                @error('work_start')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                @error('work_end')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr class="detail-row">
                            @foreach ($attendance->breakTimes as $index => $break)
                                <th class="detail-label">休憩{{ $index + 1 }}</th>
                                <td class="detail-data">
                                    <div class="break-time">
                                        <input type="time" name="break_start[]"
                                            value="{{ old('break_start.' . $index, optional($break->break_start)->format('H:i')) }}"{{ $isPending ? 'disabled' : '' }}>
                                        <span class="time-separator">～</span>
                                        <input type="time" name="break_end[]"
                                            value="{{ old('break_end.' . $index, optional($break->break_end)->format('H:i')) }}"{{ $isPending ? 'disabled' : '' }}>

                                    </div>
                                    @error('break_start.' . $index)
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                    @error('break_end.' . $index)
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </td>
                        </tr>
                        @endforeach

                        {{-- 空欄の休憩欄 --}}
                        @php
                            $emptyIndex = $attendance->breakTimes->count();
                        @endphp

                        <tr class="detail-row">
                            <th class="detail-label">休憩{{ $emptyIndex + 1 }}</th>
                            <td class="detail-data">
                                <div class="break-time">
                                    <input type="time" name="break_start[]"
                                        value="{{ old('break_start.' . $emptyIndex) }}">
                                    <span class="time-separator">～</span>
                                    <input type="time" name="break_end[]" value="{{ old('break_end.' . $emptyIndex) }}">
                                </div>

                                @error('break_start.' . $emptyIndex)
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                @error('break_end.' . $emptyIndex)
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>

                        <tr class="detail-row">
                            <th class="detail-label">備考</th>
                            <td>
                                <textarea class="reason" name="reason"></textarea>
                                @error('reason')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                    </table>
                </div>
                @if (!$isPending)
                    <div class="button-wrapper">
                        <button type="submit" class="application-button">修正</button>
                    </div>
                @else
                    <p class="pending-message">＊承認待ちのため修正はできません。</p>
                @endif
            </form>
        @endsection
