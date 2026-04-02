<x-layouts.base :title="$title ?? null" :meta="$meta ?? null" class="bg-base-200">
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle">
        <div class="drawer-content flex flex-col min-h-screen">
            {{-- Navbar --}}
            <x-layouts.navbar />
            {{-- System notifications / Flash messages --}}
            <x-layouts.flash />
            {{-- Main Page Content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
            {{-- Footer --}}
            <x-layouts.footer />
        </div>
        {{-- Sidebar (Mobile) --}}
        <x-layouts.sidebar />
    </div>
</x-layouts.base>
