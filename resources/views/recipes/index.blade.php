<x-app-layout title="All Recipes">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. ADVANCED SEARCH & FILTER SECTION --}}
            {{-- Show this only in the main list or when search is active --}}
            @if(!isset($currentCategory))
            <form action="{{ route('recipes.index') }}" method="GET"
                  class="mb-10 p-6 md:p-8 bg-base-200/40 backdrop-blur-md rounded-[2.5rem] border border-base-300/50 shadow-inner">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                    {{-- Keyword Search --}}
                    <div class="md:col-span-4">
                        <label class="label font-black text-xs uppercase tracking-widest opacity-60 mb-3">Search Keyword</label>
                        <div class="relative">
                            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 size-5 opacity-30" />
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="input input-bordered w-full rounded-2xl pl-4 bg-base-100 border-none shadow-sm focus:ring-2 focus:ring-primary"
                                   placeholder="What are you looking for? (e.g. Pasta, Chicken, Breakfast)">
                        </div>
                    </div>

                    {{-- Categories Multi-select --}}
                    <div class="md:col-span-4">
                        <label class="label font-black text-xs uppercase tracking-widest opacity-60">Filter by Multiple Categories</label>
                        <div class="flex flex-wrap gap-3 mt-1">
                            @foreach($navCategories as $category)
                                <label class="group cursor-pointer">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                           class="peer hidden"
                                        {{ (is_array(request('categories')) && in_array($category->id, request('categories'))) ? 'checked' : '' }}>
                                    <span class="btn btn-sm rounded-xl border-base-300 bg-base-100 font-medium transition-all
                                                 peer-checked:bg-primary peer-checked:text-primary-content peer-checked:border-primary
                                                 group-hover:border-primary/50">
                                        {{ $category->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="md:col-span-4 flex flex-col md:flex-row justify-between items-center gap-4 pt-4 border-t border-base-300/30">
                        <div class="text-xs font-bold opacity-40 uppercase tracking-tighter">
                            Advanced Filtering Mode
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <a href="{{ route('recipes.index') }}" class="btn btn-ghost btn-sm rounded-xl flex-1 md:flex-none">Reset</a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-xl px-10 flex-1 md:flex-none shadow-lg shadow-primary/20">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @endif

            {{-- 2. QUICK CATEGORY PILLS --}}
            <div class="mb-10">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-base-content/40 mb-4 ml-1">
                    Explore Categories
                </h2>

                <div class="mb-8 flex flex-wrap gap-2 items-center">
                    {{-- Button "All" --}}
                    <a href="{{ route('recipes.index') }}"
                       class="btn btn-sm rounded-full border-2
                    {{ !isset($currentCategory) ? 'btn-primary ring-2 ring-primary ring-offset-2' : 'border-dashed border-base-300 opacity-70' }}">
                        ✨ Show All
                    </a>

                    <div class="divider divider-horizontal mx-0 h-8 opacity-20"></div>

                    @foreach($navCategories as $cat)
                        <a href="{{ route('recipes.category', $cat->slug) }}"
                           class="btn btn-sm rounded-full transition-all {{ (isset($currentCategory) && $currentCategory->slug === $cat->slug) ? 'btn-primary shadow-md' : 'btn-outline border-base-300 hover:border-primary' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>

            {{-- 3. HEADING & RESULTS COUNT --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-black text-base-content tracking-tight">
                        @if(request('search'))
                            Results for: <span class="text-primary">"{{ request('search') }}"</span>
                        @elseif(isset($currentCategory))
                            Recipes: <span class="text-primary">{{ $currentCategory->name }}</span>
                        @else
                            All <span class="text-primary">Recipes</span>
                        @endif
                    </h1>
                    <p class="text-base-content/60 font-bold mt-2 tracking-wide uppercase text-xs">
                        {{ request('categories') ? 'Filtered by multiple categories' : 'Sorted by: Latest additions' }}
                    </p>
                </div>

                <div class="badge badge-outline p-4 font-bold opacity-50">
                    {{ $recipes->total() }} results found
                </div>
            </div>

            {{-- 4. Cards GRID --}}
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
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-20 bg-base-200/50 rounded-[3rem] border-2 border-dashed border-base-300">
                    <x-heroicon-o-document-magnifying-glass class="size-20 text-base-content/20" />
                    <h3 class="mt-4 text-xl font-black">No recipes found</h3>
                    <p class="text-base-content/60">Try checking another category or come back later!</p>
                </div>
            @endif

            {{-- 5. FOOTER NAVIGATION --}}
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
    </div>
</x-app-layout>
