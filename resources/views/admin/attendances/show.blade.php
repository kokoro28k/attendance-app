@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/show.css') }}">
@endsection

@section('content')
    <div class="attendance-detail">
        <div class="attendance-detail__inner">
            <h1 class="detail-heading">勤怠詳細</h1>
            <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="post">
                @csrf
                @method('PUT')

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
                                        <span>{{ $displayWorkStart }}</span>
                                        <span class="time-separator">～</span>
                                        <span>{{ $displayWorkEnd }}</span>
                                    @else
                                        {{-- 通常は入力欄を表示 --}}
                                        <input type="time" name="work_start"
                                            value="{{ old('work_start', $displayWorkStart) }}">
                                        <span class="time-separator">～</span>
                                        <input type="time" name="work_end"
                                            value="{{ old('work_end', $displayWorkEnd) }}">
                                    @endif
                                </div>

                                @if (!$isPending)
                                    @error('work_start')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                    @error('work_end')
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
                                            <span>{{ $b['start'] }}</span>
                                            <span class="time-separator">～</span> 
                                            <span>{{ $b['end'] }}</span>
                                        @else
                                            {{-- 通常は入力欄 --}}
                                            <input type="time" name="break_start[]"
                                                value="{{ old('break_start.' . $i, $b['start']) }}">
                                            <span class="time-separator">～</span>
                                            <input type="time" name="break_end[]"
                                                value="{{ old('break_end.' . $i, $b['end']) }}">

                                            @error('break_start.' . $i)
                                                <p class="error-message">{{ $message }}</p>
                                            @enderror
                                            @error('break_end.' . $i)
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
                                <th class="detail-label">{{ $emptyIndex === 0 ? '休憩' : '休憩' . ($emptyIndex + 1) }}</th>
                                <td class="detail-data">
                                    <div class="break-time">
                                        <input type="time" name="break_start[]" value="{{ old('break_start.' . $emptyIndex) }}">
                                        <span class="time-separator">～</span>
                                        <input type="time" name="break_end[]" value="{{ old('break_end.' . $emptyIndex) }}">
                                    </div>

                                    @error('break_start.' . $emptyIndex)
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                    @error('break_end.' . $emptyIndex)
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
                                        <textarea class="reason" name="note">{{ old('note',$attendance->note) }}</textarea>

                                        @error('note')
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
