<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Content Editor')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
{{--    <meta name="auth-token" content="{{  }}">--}}
    <!-- Vite React Refresh -->
    @viteReactRefresh

    <!-- Load CSS -->
    @vite(['resources/css/app.css'])

    @yield('style')
</head>
<body class="bg-light">
<!-- Main content -->
@yield('content')

<!-- Load JavaScript -->
@vite(['resources/js/index.jsx'])

@yield('script')
</body>
</html>
