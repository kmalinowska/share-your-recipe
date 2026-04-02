<form action="{{ route('recipes.index') }}" method="GET" {{ $attributes->merge(['class' => 'w-full']) }}>
    <label class="input input-bordered flex items-center gap-2 rounded-2xl bg-base-200/50 border-none focus-within:bg-base-100 focus-within:ring-2 focus-within:ring-primary/30 transition-all h-12 pr-1">
        <input type="search"
               name="search"
               value="{{ request('search') }}"
               class="grow text-sm font-medium"
               placeholder="Search for a recipe..." />

        {{-- GLASS BUTTON (Search/Redirect Trigger) --}}
        <button type="submit" class="btn btn-primary btn-sm rounded-xl px-3 group/btn shadow-sm hover:shadow-md transition-all">
            <svg class="h-4 w-4 text-primary-content group-hover/btn:scale-110 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="3" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </g>
            </svg>
        </button>
    </label>
</form>
