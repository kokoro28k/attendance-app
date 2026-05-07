@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
    < class="login-form">
        <h1 class="login-form__heading content__heading">管理者ログイン</h1>

        <form class="login-form__form" action="/admin/login" method="post" novalidate>
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

            <button class="login-form__btn btn">管理者ログインする</button>
        </form>
        </div>
    @endsection
