@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/auth/verify-email.css') }}">
@endsection

@section('content')
    <div class="verify">
        <div class="verify-comment">
            <p class="verify-comment__text">
                登録していただいたメールアドレスに認証メールを送付しました。</p>
            <p class="verify-comment__text">
                メール認証を完了してください。</p>
        </div>

        <a class="verify-button" href="/email/verify">
             認証はこちらから
        </a>

        <form class="verify-form__resend" method="post" action="{{route('verification.send')}}">
            @csrf
            <button class="verify-resend" type="submit">
            認証メールを再送する
            </button>
        </form>
    </div>
@endsection