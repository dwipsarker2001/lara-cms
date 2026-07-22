@extends('public.layout')

@php
    use App\Support\BlockPreview;
    use App\Support\Sections;
@endphp

@section('content')
    {!! BlockPreview::render($page->sections ?? [], withGlobals: true, page: $page) !!}
@endsection
