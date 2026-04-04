@props(['recipes'])

<section class="py-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-black italic tracking-tight uppercase">Latest Recipes</h2>
        <a href="{{ route('recipes.index') }}"
           class="btn bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white hover:border-primary btn-sm gap-2 transition-all shadow-sm">
            View All
            <x-heroicon-m-arrow-right class="size-4"/>
        </a>
    </div>
    {{-- CARD GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @forelse($recipes as $recipe)
            <x-recipe-card :recipe="$recipe" />
        @empty
            <div class="col-span-full text-center py-20 bg-base-100 rounded-[3rem] border-2 border-dashed border-base-200">
                <div class="flex flex-col items-center gap-3">
                    <x-heroicon-o-document-plus class="size-12 opacity-20" />
                    <p class="opacity-50 italic font-medium">No recipes found yet. Be the first to add one!</p>
                    <a href="{{ route('recipes.create') }}" class="btn btn-primary btn-sm mt-2">Add Recipe</a>
                </div>
            </div>
        @endforelse
    </div>
</section>
