@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/applications/index.css') }}">
@endsection

@section('content')
    <div class="application-list">
        <div class="application-list__inner">
            <h1 class="application-heading">申請一覧</h1>
            <div class="border">
                <ul class="tab-list">
                    <li class="tab-item {{ $tab === 'pending' ? 'active' : '' }}"><a
                            href="{{ route('user.application.index', ['tab' => 'pending']) }}">承認待ち</a></li>
                    <li class="tab-item {{ $tab === 'approved' ? 'active' : '' }}"><a
                            href="{{ route('user.application.index', ['tab' => 'approved']) }}">承認済み</a></li>
                </ul>
            </div>
            <div class="table-wrapper">
                <table class="application-table">
                    <thead>
                        <tr class="application-row">
                            <th class="application-label">状態</th>
                            <th class="application-label">名前</th>
                            <th class="application-label">対象日時</th>
                            <th class="application-label">申請理由</th>
                            <th class="application-label">申請日時</th>
                            <th class="application-label">詳細</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($applications as $application)
                            <tr class="application-row">
                                <td class="application-data">{{ $application->status_label }}</td>
                                <td class="application-data">{{ $application->user->name }}</td>
                                <td class="application-data">{{ $application->attendance->date->format('Y/m/d') }}</td>
                                <td class="application-data">{{ $application->reason }}</td>
                                <td class="application-data">{{ $application->applied_at->format('Y/m/d') }}</td>
                                <td class="application-data">
                                    <a class="application-data__detail"
                                        href="{{ route('user.attendance.show', ['id' => $application->attendance_id, 'application_id' => $application->id]) }}">詳細</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
