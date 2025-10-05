@extends('layouts.react')

@section('title', 'Edit Content – ')



@section('content')

    @php
        $props = [
            "content"=>$content
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
