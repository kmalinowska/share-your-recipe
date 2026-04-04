@props([
    'title' => 'Welcome to Share Your Recipe',
    'subtitle' => 'Discover and share your favourite recipes',
    'image' => 'storage/images/cooking-background.jpg',
    'ctaText' => 'Explore Recipes',
    'ctaUrl' => '/recipes',
])

<div class="relative h-96 sm:h-[500px] md:h-[600px] flex items-center justify-center text-center bg-cover bg-center"
     style="background-image: url('{{asset('/storage/images/hero.jpg')}}');">

    {{-- 1. Slightly darken the entire photo --}}
    <div class="absolute inset-0 bg-black/30 z-[1]"></div>

    {{-- 2. INNER FRAME (Passe-partout) --}}
    <div class="absolute inset-1 sm:inset-10 border-2 border-white/20 z-[2] rounded-xl pointer-events-none">
        {{-- Additional shadow inside the frame to add depth --}}
        <div class="absolute inset-0 shadow-[inset_0_0_100px_rgba(0,0,0,0.6)] rounded-lg"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold mb-3 sm:mb-4 text-white drop-shadow-lg">
            {{ $title }}
        </h1>
        <p class="text-base sm:text-lg md:text-xl text-white/80 mb-4 sm:mb-6 drop-shadow">
            {{ $subtitle }}
        </p>
        <a href="{{ $ctaUrl }}"
           class="btn btn-primary btn-sm sm:btn-md md:btn-lg shadow-lg hover:scale-105 transition-transform">
            {{ $ctaText }}
        </a>
    </div>
</div>
