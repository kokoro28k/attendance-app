@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/applications/approve.css') }}">
@endsection

@section('content')
    <div class="application-approve">
        <div class="application-approve__inner">
            <h1 class="application-approve__heading">勤怠詳細</h1>
            <form action="{{ route('admin.application.approve', $application->id) }}" method="post">
                @csrf

                <div class="table-wrapper">
                    <table class="detail-table">
                        <tr class="detail-row">
                            <th class="detail-label">名前</th>
                            <td class="detail-data name-text">{{ $application->user->name }}</td>
                        </tr>
                        <tr class="detail-row">
                            <th class="detail-label">日付</th>
                            <td class="detail-data">
                                <div class="data-row">
                                    <span class="date-row__year">{{ $application->attendance->date->format('Y年') }}</span>
                                    <span class="date-row__day">
                                        {{ $application->attendance->date->format('n月j日') }}</span>
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


                        @foreach ($application->applicationBreaks as $index => $break)
                            <tr class="detail-row">
                                <th class="detail-label">{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                                <td class="detail-data">
                                    <div class="break-time">
                                        <span class="break_start">
                                            {{ optional($break->corrected_break_start)->format('H:i') }}</span>
                                        <span class="time-separator">～</span>
                                        <span class="break_end">{{ optional($break->corrected_break_end)->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr class="detail-row">
                            <th class="detail-label">備考</th>
                            <td class="detail-data detail-data--textarea">
                                <textarea class="reason" name="reason">{{ $application->reason }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="button-wrapper">
                    @if ($isPending)
                        <button type="submit" class="approve-button">承認</button>
                    @else
                        <div class="approved-button">
                            承認済み
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
