<div class="drawer lg:drawer-open">
    <input id="main-drawer" type="checkbox" class="drawer-toggle">

    {{-- DRAWER CONTENT --}}
    <div class="drawer-content flex flex-col min-h-screen">

        {{-- NAVBAR --}}
        <nav class="navbar bg-base-100 dark:bg-gray-800 border-b border-base-200 dark:border-gray-700 sticky top-0 z-50 shadow-sm">

            {{-- LEFT: logo + title --}}
            <div class="navbar-start">
                <a href="{{ route('home') }}" class="flex items-center gap-2 ml-1 hover:opacity-80 transition">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                        <span class="text-primary-content font-bold text-sm">SY</span>
                    </div>
                    <span class="font-bold text-lg hidden sm:inline">Share Your Recipe</span>
                </a>
            </div>

            {{-- RIGHT: MENU + HAMBURGER --}}
            <div class="navbar-end flex items-center gap-2">

                {{-- DESKTOP MENU --}}
                <div class="hidden lg:flex">
                    <ul class="menu menu-horizontal px-1 gap-1 text-sm">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active font-semibold' : '' }}">Home</a></li>

                        <li>
                            <details>
                                <summary class="{{ request()->routeIs('recipes.*') ? 'active font-semibold' : '' }}">All Recipes</summary>
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
                            <li><a href="{{ route('favourites.index') }}">Favourites</a></li>
                            <li><a href="{{ route('recipes.create') }}" class="btn btn-primary btn-sm ml-1">+ Create Recipe</a></li>
                            <li>
                                <details>
                                    <summary>{{ auth()->user()->name }}</summary>
                                    <ul class="bg-base-100 dark:bg-gray-800 rounded-box shadow w-48 p-2">
                                        <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                        <li><hr class="border-base-200 dark:border-gray-600"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="w-full text-left">Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                </details>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a></li>
                        @endauth
                    </ul>
                </div>

                {{-- MOBILE: HAMBURGER --}}
                <div class="lg:hidden">
                    <label for="main-drawer" class="btn btn-ghost btn-square">☰</label>
                </div>
            </div>
        </nav>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="alert alert-success mx-4 mt-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error mx-4 mt-4">{{ session('error') }}</div>
        @endif

        {{-- MAIN CONTENT --}}
        <main class="flex-1 p-4">
            <div class="max-w-7xl mx-auto">{{ $slot }}</div>
        </main>

        {{-- FOOTER --}}
        <footer class="footer footer-center p-6 bg-base-200 dark:bg-gray-900 text-base-content border-t border-base-300 dark:border-gray-700">
            <p class="text-sm">© {{ date('Y') }} Share Your Recipe</p>
        </footer>
    </div>

    {{-- SIDEBAR ONLY FOR MOBILE --}}
    <div class="drawer-side lg:hidden z-50">
        <label for="main-drawer" class="drawer-overlay"></label>
        <ul class="menu bg-base-100 dark:bg-gray-800 min-h-full w-72 p-4 gap-1">
            <li><a href="{{ route('home') }}">� Home</a></li>
            <li>
                <details>
                    <summary>� All Recipes</summary>
                    <ul>
                        <li><a href="{{ route('recipes.index') }}">All</a></li>
                        @foreach($navCategories as $cat)
                            <li><a href="{{ route('recipes.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </details>
            </li>

            @auth
                <li><a href="{{ route('favourites.index') }}">❤️ Favourites</a></li>
                <li><a href="{{ route('recipes.create') }}">➕ Create Recipe</a></li>
                <li class="mt-3 text-xs opacity-60">ACCOUNT</li>
                <li><a href="{{ route('profile.edit') }}">� Profile</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-left w-full">� Logout</button>
                    </form>
                </li>
            @else
                <li class="mt-3 text-xs opacity-60">ACCOUNT</li>
                <li><a href="{{ route('login') }}">� Login</a></li>
                <li><a href="{{ route('register') }}">� Register</a></li>
            @endauth
        </ul>
    </div>
</div>
