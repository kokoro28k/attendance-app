@extends('layouts.user')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/auth/register.css') }}">
@endsection

@section('content')
    <div class="register-form">
        <h1 class="register-form__heading content__heading">会員登録</h1>

        <form class="register-form__form" action="/register" method="post" novalidate>
            @csrf

            <div class="register-form__group">
                <label class="register-form__label" for="name">名前</label>
                <input class="register-form__input" type="text" name="name" id="name"
                    value="{{ old('name') }}">
                @error('name')
                    <p class="register-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="register-form__group">
                <label class="register-form__label" for="email">メールアドレス</label>
                <input class="register-form__input" type="email" name="email" id="email"
                    value="{{ old('email') }}">
                @error('email')
                    <p class="register-form__error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="register-form__group">
                <label class="register-form__label" for="password">パスワード</label>
                <input class="register-form__input" type="password" name="password" id="password">
            </div>

            <div class="register-form__group">
                <label class="register-form__label" for="password_confirmation">パスワード確認</label>
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation">
                @error('password')
                    <p class="register-form__error-message">{{ $message }}</p>
                @enderror
            </div>
            <button class="register-form__btn btn">登録する</button>
        </form>

        <a class="register-form__link" href="/login">ログインはこちら</a>
    </div>
@endsection
