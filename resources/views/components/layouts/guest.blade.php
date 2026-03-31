<x-layouts.base :title="config('app.name')" class="bg-base-200 ">
    <div class="min-h-screen flex flex-col items-center pt-16 sm:pt-20 pb-20 bg-base-200">

        {{-- Logo Section --}}
        <div class="mb-8">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 group">
                <div class="avatar transition-transform group-hover:scale-105 duration-300">
                    <div class="ring-primary ring-offset-base-100 w-16 h-16 rounded-full ring-2 ring-offset-4">
                        <img src="{{ asset('storage/images/main_logo.jpg') }}" alt="Logo" />
                    </div>
                </div>
                <span class="font-extrabold text-2xl tracking-tight bg-gradient-to-r from-primary to-blue-500 bg-clip-text text-transparent mt-2">
                    Share Your Recipe
                </span>
            </a>
        </div>

        {{-- Flash Messages --}}
        <div class="w-full sm:max-w-md">
            <x-layouts.flash />
        </div>

        {{-- Form Card --}}
        <div class="w-full sm:max-w-md mt-4 px-8 py-8 bg-base-100 shadow-2xl overflow-hidden sm:rounded-3xl border border-base-300">
            {{ $slot }}
        </div>

        {{-- Back Link --}}
        <div class="mt-8 mb-4 text-sm">
            <a href="{{ route('home') }}" class="flex items-center gap-2 link link-hover opacity-60 hover:opacity-100 transition-opacity font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to home
            </a>
        </div>
    </div>
</x-layouts.base>
