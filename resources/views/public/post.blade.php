@extends('public.layout')

@section('content')
    @foreach ($globals as $section)
        @php $block = $registry->get($section['name'] ?? ''); @endphp
        @if (($section['enabled'] ?? true) && $block && view()->exists($block->view()))
            @include($block->view(), [
                'data' => $section['data'] ?? [],
                '_key' => $section['_key'] ?? '',
                'preview' => false,
            ])
        @endif
    @endforeach

    <article class="py-16">
        <div class="max-w-4xl mx-auto px-6">
            @if($post->banner_img || $post->hero_img)
                <div class="w-full h-72 md:h-96 rounded-2xl overflow-hidden mb-10 bg-gray-100">
                    <img src="{{ $post->banner_img ?: $post->hero_img }}" alt="{{ $post->title }}" class="w-full h-full object-cover" />
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 mb-4">
                <span>By <span class="font-medium text-gray-700">{{ $post->author ?: 'Anonymous' }}</span></span>
                @if($post->date)
                    <span>•</span>
                    <span>{{ \Carbon\Carbon::parse($post->date)->format('M d, Y') }}</span>
                @endif
                @if($post->tags && count($post->tags) > 0)
                    <span>•</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <span class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-xs font-medium">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p class="text-lg text-gray-500 leading-relaxed mb-8">{{ $post->excerpt }}</p>
            @endif

            <div class="prose prose-gray max-w-none">
                {!! nl2br(e($post->body)) !!}
            </div>
        </div>
    </article>
@endsection
