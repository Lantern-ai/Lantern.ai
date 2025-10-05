@extends('layouts.app')
@section('content')
    <form method="post" action="{{route('script.store')}}">
        @csrf
        <input type="text" name="title">
        <input type="text" name="language">
        <textarea name="description"></textarea>
        <input type="submit">
    </form>
@endsection
