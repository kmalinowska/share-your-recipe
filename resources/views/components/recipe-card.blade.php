@props(['recipe', 'userFavourites' => []])

<div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-md transition-all duration-300 rounded-[2rem] overflow-hidden group h-full flex flex-col">
    {{-- Image section --}}
    <figure class="relative h-56 w-full overflow-hidden shrink-0">
        @if($recipe->image_path)
            <img src="{{ Str::startsWith($recipe->image_path, 'http') ? $recipe->image_path : asset('storage/' . $recipe->image_path) }}"
                 alt="{{ $recipe->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
        @else
            {{-- Placeholder without available image --}}
            <div class="w-full h-full bg-base-200 flex flex-col items-center justify-center text-base-content/20">
                <x-heroicon-o-photo class="size-16" />
                <span class="text-xs font-bold uppercase tracking-widest mt-2">No photo available</span>
            </div>
        @endif

        {{-- Category badge in the image --}}
        <div class="absolute top-4 left-4">
            <span class="badge badge-primary font-bold uppercase text-[10px] tracking-wider px-3 shadow-sm">
                {{ $recipe->category->name }}
            </span>
        </div>
    </figure>

    {{-- Card body --}}
    <div class="card-body p-5 gap-3 flex flex-col flex-grow">
        {{-- Header: Title, Author and Date --}}
        <div class="flex flex-col gap-1.5">
            {{-- line-clamp-2 prevents long titles from pushing other elements --}}
            <h2 class="card-title text-xl font-black leading-tight text-base-content group-hover:text-primary transition-colors line-clamp-2 min-h-[3.5rem] items-start">
                {{ $recipe->title }}
            </h2>

            <div class="flex flex-col gap-0.5">
                {{-- Author --}}
                <p class="text-[10px] opacity-40 font-bold tracking-widest uppercase">
                    Author:
                    <span class="text-xs text-base-content opacity-100 font-black hover:underline cursor-pointer">
                        {{ $recipe->user->name }}
                    </span>
                </p>
                <p class="text-[10px] opacity-40 font-bold uppercase tracking-widest">
                    Date added:
                    <span class="text-xs text-base-content opacity-100 font-black tracking-normal">
                        {{ $recipe->created_at->format('d.m.Y H:i') }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Tags (Badges) - flex-grow pushes the bottom section down --}}
        <div class="flex flex-wrap gap-1 mt-1 flex-grow content-start">
            @foreach($recipe->tags as $tag)
                <span class="badge badge-ghost badge-sm text-[10px] uppercase font-bold opacity-70">{{ $tag->name }}</span>
            @endforeach
        </div>

        {{-- Information Section: Time and Heart --}}
        <div class="flex items-center justify-between border-t border-base-100 pt-4 mt-auto">
            <div class="flex items-center gap-1.5 opacity-70">
                <x-heroicon-o-clock class="size-5" />
                <span class="text-sm font-bold">{{ $recipe->preparation_time }} min</span>
            </div>

            {{-- Favourites (Heart) --}}
            <div class="{{ !auth()->check() ? 'tooltip tooltip-left' : '' }}"
                 data-tip="{{ !auth()->check() ? 'Login to add favourites' : '' }}">

                @auth
                    <form action="{{ route('favourites.toggle', $recipe) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-circle btn-sm group/heart">
                            @if(in_array($recipe->id, $userFavourites ?? []))
                                {{-- Full heart for likesv--}}
                                <x-heroicon-s-heart class="size-6 text-error transition-transform group-hover/heart:scale-110" />
                            @else
                                {{-- Empty heart for unlikes --}}
                                <x-heroicon-o-heart class="size-6 text-base-content/20 group-hover/heart:text-error/50 transition-colors" />
                            @endif
                        </button>
                    </form>
                @else
                    <button class="btn btn-ghost btn-circle btn-sm cursor-not-allowed">
                        <x-heroicon-o-heart class="size-6 text-base-content/20" />
                    </button>
                @endauth
            </div>
        </div>

        {{-- Details button --}}
        <div class="card-actions justify-center mt-4">
            <a href="{{ route('recipes.show', $recipe) }}"
               class="btn btn-primary btn-wide rounded-xl shadow-sm hover:shadow-md transition-all group/btn">
                Details
                <x-heroicon-m-arrow-right class="size-4 group-hover/btn:translate-x-1 transition-transform" />
            </a>
        </div>
    </div>
</div>
