@extends('public.layout')

@section('content')
    @php
        $registry = app(\App\Blocks\BlockRegistry::class);
        $sections = \App\Support\Sections::withGlobals($page->sections ?? []);
    @endphp
    @foreach ($sections as $section)
        @php $block = $registry->get($section['name'] ?? ''); @endphp
        @if (($section['enabled'] ?? true) && $block && view()->exists($block->view()))
            @include($block->view(), [
                'data' => $section['data'] ?? [],
                '_key' => $section['_key'] ?? '',
                'preview' => false,
            ])
        @endif
    @endforeach
@endsection
