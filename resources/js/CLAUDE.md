this projects backend  is in laravel. 
React is compiled using vite and send to the frontend in a blade file like this:

react.blade.php
```
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Content Editor')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

```

editor.blade.php file
```
@extends('layouts.react')

@section('title', 'Edit Content – ')



@section('content')
@php
$props = [

      ];
    @endphp
    <div
        id="react-root"
        data-component="ContentEditor"
        data-props='@json($props)'
    ></div>

@endsection

@push('script')

@endpush

```

