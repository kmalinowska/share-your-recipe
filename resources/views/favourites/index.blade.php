<x-app-layout title="My Favourites">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-black mb-10 text-base-content">
                Your <span class="text-primary">Favourites</span> ❤️
            </h1>

            @if($recipes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($recipes as $recipe)
                        <x-recipe-card :recipe="$recipe" :userFavourites="$userFavourites" />
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $favourites_paginated->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-base-200 rounded-[3rem]">
                    <p class="text-xl opacity-50">You haven't added any favourites yet.</p>
                    <a href="{{ route('recipes.index') }}" class="btn btn-primary mt-4 rounded-xl">Explore Recipes</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
