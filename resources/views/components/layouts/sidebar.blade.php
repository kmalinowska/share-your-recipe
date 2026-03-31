<div class="drawer-side lg:hidden z-50" data-theme="light">
    <label for="main-drawer" class="drawer-overlay"></label>
    <ul class="menu bg-base-100 min-h-full w-72 p-4 gap-1">
        <li>
            <a href="{{ route('home') }}">
                <x-heroicon-o-home class="size-6"/>
                Home
            </a>
        </li>
        <li>
            <details>
                <summary>
                    <x-heroicon-o-book-open class="size-6"/>
                    All Recipes</summary>
                <ul>
                    <li>
                        <a href="{{ route('recipes.index') }}">All</a></li>
                    @foreach($navCategories as $cat)
                        <li><a href="{{ route('recipes.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </details>
        </li>

        @auth
            <li>
                <a href="{{ route('favourites.index') }}">
                    <x-heroicon-o-heart class="size-6"/>
                    Favourites
                </a>
            </li>
            <li>
                <a href="{{ route('recipes.create') }}">
                    <x-heroicon-o-plus-circle class="size-6"/>
                    Create Recipe
                </a>
            </li>
            <li class="mt-3 text-xs opacity-60">ACCOUNT</li>
            <li>
                <a href="{{ route('profile.edit') }}">
                    <x-heroicon-o-user class="size-6"/>
                    Profile
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-left w-full">
                        <x-heroicon-o-arrow-left-start-on-rectangle class="size-6"/>
                        Logout
                    </button>
                </form>
            </li>
        @else
            <li class="mt-3 text-xs opacity-60">ACCOUNT</li>
            <li>
                <a href="{{ route('login') }}">
                    <x-heroicon-o-key class="size-6"/>
                    Login
                </a>
            </li>
            <li>
                <a href="{{ route('register') }}">
                    <x-heroicon-o-user-plus class="size-6"/>
                    Register
                </a>
            </li>
        @endauth
    </ul>
</div>
