@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendances/show.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        <div class="attendance-detail__inner">
            <h1 class="detail-heading">勤怠詳細</h1>
            <form action="{{ route('user.application.store') }}" method="post" autocomplete="off">
                @csrf
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

                <div class="table-wrapper">
                    <table class="detail-table">
                        <tr class="detail-row">
                            <th class="detail-label">名前</th>
                            <td class="detail-data name-text">{{ $attendance->user->name }}</td>
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

                                    @if ($isPending)
                                        {{-- 承認待ちの場合、テキスト表示のみ --}}
                                        <span>{{ $displayWorkStart }} ～ {{ $displayWorkEnd }}</span>
                                    @else
                                        {{-- 通常は入力欄を表示 --}}
                                        <input type="time" name="corrected_work_start"
                                            value="{{ old('corrected_work_start', $displayWorkStart) }}">
                                        <span class="time-separator">～</span>
                                        <input type="time" name="corrected_work_end"
                                            value="{{ old('corrected_work_end', $displayWorkEnd) }}">
                                    @endif
                                </div>

                                @if (!$isPending)
                                    @error('corrected_work_start')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                    @error('corrected_work_end')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                @endif
                            </td>
                        </tr>

                        @foreach ($displayBreaks as $i => $b)
                            <tr class="detail-row">
                                <th class="detail-label">{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</th>
                                <td class="detail-data">
                                    <div class="break-time">

                                        @if ($isPending)
                                            {{-- 承認待ちの場合は、テキスト表示 --}}
                                            <span>{{ $b['start'] }} ～ {{ $b['end'] }}</span>
                                        @else
                                            {{-- 通常は入力欄 --}}
                                            <input type="time" name="corrected_break_start[]"
                                                value="{{ old('corrected_break_start.' . $i, $b['start']) }}">
                                            <span class="time-separator">～</span>
                                            <input type="time" name="corrected_break_end[]"
                                                value="{{ old('corrected_break_end.' . $i, $b['end']) }}">

                                            {{-- エラーも通常時だけ --}}
                                            @error('corrected_break_start.' . $i)
                                                <p class="error-message">{{ $message }}</p>
                                            @enderror
                                            @error('corrected_break_end.' . $i)
                                                <p class="error-message">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        {{-- 空欄の休憩欄 --}}
                        @php
                            $emptyIndex = $attendance->breakTimes->count();
                        @endphp
                        @if (!$isPending)
                            <tr class="detail-row">
                                <th class="detail-label"> {{ $emptyIndex === 0 ? '休憩' : '休憩' . ($emptyIndex + 1) }}</th>
                                <td class="detail-data">
                                    <div class="break-time">
                                        <div class="break-time">
                                            <input type="time" name="corrected_break_start[]"
                                                value="{{ old('corrected_break_start.' . $emptyIndex) }}">
                                            <span class="time-separator">～</span>
                                            <input type="time" name="corrected_break_end[]"
                                                value="{{ old('corrected_break_end.' . $emptyIndex) }}">
                                        </div>

                                        @error('corrected_break_start.' . $emptyIndex)
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                        @error('corrected_break_end.' . $emptyIndex)
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                        @endif
                        </td>
                        </tr>

                        <tr class="detail-row">
                            <th class="detail-label">備考</th>
                            <td class="detail-data detail-data--textarea">
                                @if ($isPending)
                                    <p class="reason-text">{{ $displayReason }}</p>
                                @else
                                    <textarea class="reason" name="reason">{{ old('reason', $displayReason) }}</textarea>

                                    @error('reason')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if (!$isPending)
                        <div class="button-wrapper">
                            <button type="submit" class="application-button">修正</button>
                        </div>
                    @else
                        <p class="pending-message">＊承認待ちのため修正はできません。</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
