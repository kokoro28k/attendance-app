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

            {{-- 会員登録画面、ログイン画面はナビケーションバーを表示しない --}}

            <nav class="header-nav">
                <ul>
                    @if ($attendance && $attendance->status === $STATUS_FINISHED)
                        <li><a href="{{ route('user.attendance.index') }}">今月の出勤一覧</a>
                        </li>
                        <li><a href="{{ route('user.application.index') }}">申請一覧</a>
                        </li>
                    @else
                        <li><a href="{{ route('user.attendance.create') }}">勤怠</a>
                        </li>
                        <li> <a href="{{ route('user.attendance.index') }}">勤怠一覧</a>
                        </li>
                        <li><a href="{{ route('user.application.index') }}">申請</a>
                        </li>
                    @endif

                    <li>
                        <form action="/logout" method="post">
                            @csrf
                            <input class="header-nav__link" type="submit" value="ログアウト">
                        </form>
                    </li>
                </ul>
            </nav>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>
