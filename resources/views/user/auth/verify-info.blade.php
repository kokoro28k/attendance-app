@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/auth/verify-info.css') }}">
@endsection

@section('content')
    <div class="verify-info">
        <div class="verify-info__message">
            <p class="verify-info__text">
                認証メールを送信しました。</p>
            <p class="verify-info__text">
                メール内のリンクをクリックして認証を完了してください。</p>
            <p class="verify-info__text">認証後はログインできます。</p>
        </div>
    </div>
@endsection