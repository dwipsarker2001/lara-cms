@php
    $allPosts = \App\Models\Post::where('published', true)->orderBy('date', 'desc')->take(4)->get();
    $featured = $allPosts->shift();
    $sidePosts = $allPosts;
@endphp
<section data-block="latestBlog" class="py-20">
    <div class="max-w-6xl mx-auto px-6">
        @if($data['headline'] ?? false)
            <h2 data-edit="headline" class="text-center text-2xl md:text-3xl font-bold text-gray-900">{{ $data['headline'] }}</h2>
        @endif
        @if($data['description'] ?? false)
            <p data-edit="description" class="mx-auto mt-3 max-w-xl text-center text-gray-500">{{ $data['description'] }}</p>
        @endif

        @if($allPosts->isEmpty())
            <p class="mt-10 text-center text-sm text-gray-400">No blog posts yet.</p>
        @else
            <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-8 lg:items-stretch pb-2">
                {{-- Featured post --}}
                <a href="/blogs/{{ $featured->slug }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:border-gray-200">
                    <div class="relative block min-h-[260px] flex-1 overflow-hidden bg-gray-100">
                        @php $img = $featured->hero_img ?? $featured->banner_img ?? null; @endphp
                        @if($img)
                            <img src="{{ $img }}" alt="{{ $featured->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" />
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>By <span class="text-gray-700 font-medium">{{ $featured->author ?: 'Anonymous' }}</span></span>
                            @if($featured->date)
                                <span>•</span>
                                <span>{{ \Carbon\Carbon::parse($featured->date)->format('M j, Y') }}</span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold mt-3 transition-colors group-hover:text-brand">{{ $featured->title }}</h3>
                        @php
                            $preview = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::squish(strip_tags($featured->body ?? '')), 180);
                        @endphp
                        @if($preview)
                            <p class="mt-3 text-sm text-gray-500 leading-relaxed line-clamp-3">{{ $preview }}</p>
                        @endif
                        <span class="inline-flex items-center gap-1 text-brand font-semibold mt-4 transition-all group-hover:gap-2">
                            View Post
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                        </span>
                    </div>
                </a>

                {{-- Side posts --}}
                <div class="flex flex-col gap-6">
                    @foreach($sidePosts as $post)
                        @php
                            $tag = $post->tags[0] ?? null;
                            $img = $post->hero_img ?? $post->banner_img ?? null;
                            $preview = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::squish(strip_tags($post->body ?? '')), 180);
                        @endphp
                        <a href="/blogs/{{ $post->slug }}" class="group flex flex-col gap-3 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:border-gray-200 lg:overflow-visible lg:flex-row lg:gap-5 lg:items-start lg:rounded-xl lg:p-2 lg:-m-2 lg:hover:bg-gray-50 lg:hover:translate-y-0">
                            <div class="relative w-full h-[180px] overflow-hidden bg-gray-100 lg:shrink-0 lg:w-[220px] lg:h-[140px] lg:rounded-lg">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $post->title }}" class="pointer-events-none absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105 lg:group-hover:scale-110" />
                                @endif
                                @if($post->date)
                                    @php $dt = \Carbon\Carbon::parse($post->date); @endphp
                                    <div class="absolute top-3 left-3 bg-brand text-white text-center rounded-full w-11 h-11 flex flex-col items-center justify-center text-xs leading-tight pointer-events-none">
                                        <span class="font-bold text-sm">{{ $dt->format('j') }}</span>
                                        <span class="text-[10px]">{{ $dt->format('M') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col justify-between flex-1 lg:p-0 lg:min-h-[140px]">
                                <div>
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <span>By <span class="text-gray-700 font-medium">{{ $post->author ?: 'Anonymous' }}</span></span>
                                        @if($tag)
                                            <span>•</span>
                                            <span>{{ $tag }}</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-xl mt-2 line-clamp-2 transition-colors group-hover:text-brand">{{ $post->title }}</h4>
                                    @if($preview)
                                        <p class="mt-1.5 text-sm text-gray-500 leading-relaxed line-clamp-3">{{ $preview }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
