@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/index.css') }}">
@endsection

@section('content')
    <div class="staff-list">
        <div class="staff-list__inner">
            <h1 class="list-heading">スタッフ一覧</h1>
            <div class="table-wrapper">
                <table class="list-table">
                    <thead>
                        <tr class="list-row">
                            <th class="list-label">名前</th>
                            <th class="list-label">メールアドレス</th>
                            <th class="list-label">月次勤怠</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr class="list-row">
                                <td class="list-data">{{ $user->name }}</td>
                                <td class="list-data">{{ $user->email }}</td>
                                <td class="list-data"><a class="list-data__detail"
                                        href="{{ route('staff.attendance', ['id' => $user->id]) }}">詳細</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
