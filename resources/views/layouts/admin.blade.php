<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header-logo">
                <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </div>

            {{-- ログイン画面はナビケーションバーを表示しない --}}

            @if (Auth::check())
                <nav class="header-nav">
                    <ul>
                        <li><a href="{{ route('admin.attendance.index') }}">勤怠一覧</a>
                        </li>
                        <li> <a href="{{ route('staff.index') }}">スタッフ一覧</a>
                        </li>
                        <li><a href="{{ route('admin.application.list') }}">申請一覧</a>
                        </li>
                        <li>
                            <form action="/logout" method="post">
                                @csrf
                                <input class="header__logout" type="submit" value="ログアウト">
                            </form>
                        </li>
                    </ul>
                </nav>
            @endif
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>
