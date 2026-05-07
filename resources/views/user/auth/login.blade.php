@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/auth/login.css') }}">
@endsection

@section('content')
    <div class="login-form">
        <h1 class="login-form__heading content__heading">ログイン</h1>

        <form class="login-form__form" action="/login" method="post" novalidate>
            @csrf

            <div class="login-form__group">
                <label class="login-form__label" for="email">メールアドレス</label>
                <input class="login-form__input" type="email" name="email" id="email" value="{{ old('email') }}">
                @error('email')
                    <p class="login-form__error-message"> {{ $message }}</p>
                @enderror
            </div>

            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input class="login-form__input" type="password" name="password" id="password">
                @error('password')
                    <p class="login-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            <button class="login-form__btn btn">ログインする</button>
        </form>

        <a class="login-form__link" href="/register">会員登録はこちら</a>
    </div>
@endsection
