<nav class="navbar bg-base-100 border-b border-base-200 sticky top-0 z-50 shadow-sm">
    {{--    LEFT: logo + title--}}
    <div class="navbar-start">
        <a href="{{ route('home') }}" class="flex items-center gap-2 ml-1 hover:opacity-80 transition">
            {{-- Logotype & page title --}}
            <div class="avatar">
                <div class="ring-primary ring-offset-base-100 w-10 h-10 rounded-full ring-2 ring-offset-2">
                    <img src="{{ asset('storage/images/main_logo.jpg') }}" alt="Share Your Recipe Logo" />
                </div>
            </div>
            <span class="font-extrabold text-xl hidden sm:inline tracking-tight bg-gradient-to-r from-primary to-blue-600 bg-clip-text text-transparent">
                Share Your Recipe
            </span>
        </a>
    </div>

    {{-- RIGHT: MENU + HAMBURGER --}}
    <div class="navbar-end flex items-center gap-2">

        {{-- DESKTOP MENU --}}
        <div class="hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-1 text-sm">
                <li>
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active font-semibold' : '' }}">
                        <x-heroicon-o-home class="size-6"/>
                        Home
                    </a>
                </li>

                <li>
                    <details>
                        <summary class="{{ request()->routeIs('recipes.*') ? 'active font-semibold' : '' }}">
                            <x-heroicon-o-book-open class="size-6"/>
                            All Recipes
                        </summary>
                        <ul class="bg-base-100 dark:bg-gray-800 rounded-box shadow w-52 p-2">
                            <li><a href="{{ route('recipes.index') }}">All</a></li>
                            <li><hr class="my-1 border-base-200 dark:border-gray-600"></li>
                            @foreach($navCategories as $cat)
                                <li>
                                    <a href="{{ route('recipes.index', ['category' => $cat->slug]) }}"
                                       class="{{ request('category') === $cat->slug ? 'active' : '' }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
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
                        <a href="{{ route('recipes.create') }}" class="btn btn-primary btn-sm ml-1">+
                            <x-heroicon-o-plus-circle class="size-6"/>
                            Create Recipe
                        </a>
                    </li>
                    <li>
                        <details>
                            <summary>{{ auth()->user()->name }}</summary>
                            <ul class="bg-base-100 dark:bg-gray-800 rounded-box shadow w-48 p-2">
                                <li>
                                    <a href="{{ route('profile.edit') }}">
                                        <x-heroicon-o-user class="size-6"/>
                                        Profile
                                    </a>
                                </li>
                                <li><hr class="border-base-200 dark:border-gray-600"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left">
                                            <x-heroicon-o-arrow-left-start-on-rectangle class="size-6"/>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </details>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}">
                            <x-heroicon-o-key class="size-6"/>
                            Login
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            <x-heroicon-o-user-plus class="size-6"/>
                            Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>

        {{-- MOBILE: HAMBURGER --}}
        <div class="lg:hidden">
            <label for="main-drawer" class="btn btn-ghost btn-square">
                <x-heroicon-o-bars-4 class="size-6"/>
            </label>
        </div>
    </div>
</nav>
