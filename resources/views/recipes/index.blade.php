<x-app-layout title="All Recipes">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Heading --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-black text-base-content tracking-tight">
                        @if(isset($currentCategory))
                            Recipes: <span class="text-primary">{{ $currentCategory->name }}</span>
                        @else
                            All <span class="text-primary">Recipes</span>
                        @endif
                    </h1>
                    <p class="text-base-content/60 font-bold mt-2 tracking-wide uppercase text-xs">
                        Sorted by: Latest additions
                    </p>
                </div>

                {{-- Results count --}}
                <div class="badge badge-outline p-4 font-bold opacity-50">
                    {{ $recipes->total() }} results found
                </div>
            </div>

            {{-- Cards --}}
            @if($recipes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($recipes as $recipe)
                        <x-recipe-card :recipe="$recipe" :userFavourites="$userFavourites" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-16 flex justify-center">
                    {{ $recipes->links() }}
                </div>
            @else
                {{-- Empty --}}
                <div class="flex flex-col items-center justify-center py-20 bg-base-200/50 rounded-[3rem] border-2 border-dashed border-base-300">
                    <x-heroicon-o-document-magnifying-glass class="size-20 text-base-content/20" />
                    <h3 class="mt-4 text-xl font-black">No recipes found</h3>
                    <p class="text-base-content/60">Try checking another category or come back later!</p>
                </div>
            @endif

            <div class="mt-12 mb-6 flex flex-wrap justify-center gap-4">
                {{-- Back to home button  --}}
                <a href="{{ route('home') }}" class="btn btn-outline rounded-2xl gap-2 hover:bg-base-200 hover:text-base-content transition-all px-8">
                    <x-heroicon-o-home class="size-5" />
                    Back to Home
                </a>

                {{-- Back to all recipes --}}
                {{-- Only when user is in category page --}}
                @if(isset($currentCategory))
                    <a href="{{ route('recipes.index') }}" class="btn btn-primary rounded-2xl gap-2 shadow-md hover:shadow-lg transition-all px-8">
                        <x-heroicon-o-book-open class="size-5" />
                        View All Recipes
                    </a>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
