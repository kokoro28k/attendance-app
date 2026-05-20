@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/applications/approve.css') }}">
@endsection

@section('content')
    <div class="application-approve">
        <div class="application-approve__inner">
            <h1 class="application-approve__heading">勤怠詳細</h1>
            <form action="{{ route('admin.attendance.approve', $application->id) }}" method="post">
                @csrf

                <div class="table-wrapper">
                    <table class="detail-table">
                        <tr class="detail-row">
                            <th class="detail-label">名前</th>
                            <td class="detail-data">{{ $application->user->name }}</td>
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
                                    <span class="work_start">
                                        {{ optional($application->corrected_work_start)->format('H:i') }}</span>
                                    <span class="time-separator">～</span>
                                    <span class="work_end">
                                        {{ optional($application->corrected_work_end)->format('H:i') }}</span>
                                </div>
                            </td>
                        </tr>

                        <tr class="detail-row">
                            @foreach ($application->applicationBreaks as $index => $break)
                                <th class="detail-label">休憩{{ $index + 1 }}</th>
                                <td class="detail-data">
                                    <div class="break-time">
                                        <span class="break_start">
                                            {{ optional($break->corrected_break_start)->format('H:i') }}</span>
                                        <span class="time-separator">～</span>
                                        <span class="break_end">{{ optional($break->corrected_break_end)->format('H:i') }}>

                                    </div>
                                </td>
                        </tr>
                        @endforeach

                        {{-- 空欄の休憩欄を1つ追加 --}}
                        <tr class="detail-row">
                            <th class="detail-label">休憩{{ $application->applicationBreaks->count() + 1 }}</th>
                            <td></td>
                        </tr>

                        <tr class="detail-row">
                            <th class="detail-label">備考</th>
                            <td>
                                <textarea class="reason" name="reason">{{ $application->reason }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                @if ($isPending)
                    <div class="button-wrapper">
                        <button type="submit" class="application-button">承認</button>
                    </div>
                @else
                    <div class="approve-button">
                        <span class="approve-message">承認済み</span>
                    </div>
                @endif
            </form>
        @endsection
