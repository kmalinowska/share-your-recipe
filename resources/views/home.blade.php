<x-app-layout :title="'Home'">
<div class="relative h-96 sm:h-[500px] md:h-[600px] flex items-center justify-center text-center bg-cover bg-center"
         style="background-image: url('{{asset('/storage/images/cooking-background.jpg')}}');">

        <!-- Gradient / Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/25 to-black/50"></div>

        <!-- Content below overlay -->
        <div class="relative z-10 px-4 sm:px-6 md:px-8">
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold mb-3 sm:mb-4 text-white drop-shadow-lg">
                Welcome to Share Your Recipe
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-white/80 mb-4 sm:mb-6 drop-shadow">
                Discover and share your favourite recipes
            </p>
            <!-- Button CTA -->
            <a href="{{ route('recipes.index') }}"
               class="btn btn-primary btn-sm sm:btn-md md:btn-lg shadow-lg hover:scale-105 transition-transform">
                Explore Recipes
            </a>
        </div>
    </div>
</x-app-layout>
