<x-app-layout :title="$recipe->title">
    <div class="py-12 bg-base-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. HEADER: Title & Meta --}}
            <div class="text-center mb-10">
                <div class="badge badge-primary badge-outline mb-4 uppercase font-bold tracking-widest text-xs p-3">
                    {{ $recipe->category->name }}
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-base-content tracking-tight mb-4">
                    {{ $recipe->title }}
                </h1>
                <div class="flex items-center justify-center gap-4 text-sm text-base-content/60 font-medium">
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-user class="size-4" />
                        {{ $recipe->user->name }}
                    </span>
                    <span class="opacity-20">|</span>
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-calendar class="size-4" />
                        {{ $recipe->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>

            {{-- 2. MAIN IMAGE --}}
            <div class="relative group mb-12">
                <div class="aspect-[21/9] w-full overflow-hidden rounded-[3rem] shadow-2xl border border-base-200">
                    <img src="{{ Str::startsWith($recipe->image_path, 'http') ? $recipe->image_path : asset('storage/' . $recipe->image_path) }}"
                         alt="{{ $recipe->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                </div>
            </div>

            {{-- 2.5 QUICK INFO: Time & Favourite --}}
            <div class="flex justify-center mb-12">
                <div class="inline-flex items-center justify-between w-full max-w-md bg-base-200 backdrop-blur-sm rounded-3xl p-4 border border-base-200 shadow-sm">

                    {{-- Preparation Time --}}
                    <div class="flex items-center gap-3 px-4">
                        <div class="p-2 bg-primary/10 rounded-xl text-primary">
                            <x-heroicon-o-clock class="size-6" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-widest font-bold opacity-50">Preparation time</span>
                            <span class="text-base font-black text-base-content">{{ $recipe->preparation_time }} min</span>
                        </div>
                    </div>

                    <div class="h-10 w-[1px] bg-base-300"></div> {{-- Vertical Divider --}}

                    {{-- Favourite Button --}}
                    <div class="px-4">
                        <div class="{{ !auth()->check() ? 'tooltip tooltip-top' : '' }}"
                             data-tip="{{ !auth()->check() ? 'Login to add favourites' : '' }}">

                            @auth
                                <form action="{{ route('favourites.toggle', $recipe) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost hover:bg-error/10 group rounded-2xl gap-3">
                                        <x-heroicon-s-heart class="size-7 {{ in_array($recipe->id, $userFavourites ?? []) ? 'text-error' : 'text-base-content/20 group-hover:text-error/50' }} transition-colors" />
                                        <span class="font-bold text-sm">Favourite</span>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-ghost no-animation cursor-not-allowed gap-3 opacity-50">
                                    <x-heroicon-o-heart class="size-7 text-base-content/20" />
                                    <span class="font-bold text-sm text-base-content/40">Favourite</span>
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

                {{-- LEFT COLUMN: Ingredients & Tags --}}
                <div class="lg:col-span-1 space-y-8 sticky top-24">

                    {{-- Ingredients Card --}}
                    <div class="bg-base-200 rounded-[2rem] p-8 border border-base-200 shadow-sm">
                        <h3 class="text-2xl font-black text-base-content m-0 flex items-center gap-3 h-9">
                            <x-heroicon-o-list-bullet class="size-7 text-primary" />
                            Ingredients
                        </h3>

                        <ul class="divide-y divide-base-300/50 mt-8">
                            @forelse($recipe->ingredients as $ingredient)
                                <li class="py-3 flex justify-between items-start gap-4">
                        <span class="text-base-content/90 font-medium italic">
                            {{ $ingredient->name }}
                        </span>
                                    <span class="text-primary font-bold text-sm bg-primary/10 px-3 py-1 rounded-full whitespace-nowrap">
                            {{ $ingredient->pivot->quantity }} {{ $ingredient->pivot->unit ?? '' }}
                        </span>
                                </li>
                            @empty
                                <p class="text-sm opacity-50 italic">No ingredients listed.</p>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Tags Card --}}
                    @if($recipe->tags->count() > 0)
                        <div class="bg-base-200 rounded-[2rem] p-8 border border-base-200/50 shadow-sm">
                            <div class="flex items-center gap-2 mb-6 text-base-content/50">
                                <x-heroicon-o-tag class="size-5 text-primary" />
                                <span class="text-sm font-bold uppercase tracking-widest">Recipe Tags</span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @foreach($recipe->tags as $tag)
                                    <a href="#" class="badge badge-ghost hover:badge-primary hover:text-primary-content border-none p-4 font-medium transition-all lowercase bg-base-100 hover:scale-105 shadow-sm">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- RIGHT COLUMN: Preparation --}}
                <div class="lg:col-span-2 lg:pt-8 pt-0">
                    <h3 class="text-2xl font-black text-base-content m-0 flex items-center gap-3 h-9">
                        <x-heroicon-o-fire class="size-7 text-primary" />
                        Preparation
                    </h3>

                    <div class="mt-8 text-base-content/80 text-lg leading-relaxed whitespace-pre-line border-l-4 border-primary/10 pl-8">
                        {{ $recipe->preparation }}
                    </div>
                </div>
            </div>

            {{-- 4. NAVIGATION FOOTER --}}
            <div class="mt-20 flex flex-wrap justify-center gap-4 pt-10 border-t border-base-200">
                <a href="{{ route('home') }}" class="btn btn-outline rounded-2xl gap-2 px-8 shadow-sm">
                    <x-heroicon-o-home class="size-5" />
                    Home
                </a>
                <a href="{{ route('recipes.index') }}" class="btn btn-outline rounded-2xl gap-2 px-8 shadow-sm">
                    <x-heroicon-o-book-open class="size-5" />
                    All Recipes
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
