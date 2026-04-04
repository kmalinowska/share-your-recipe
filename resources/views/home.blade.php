<x-app-layout title="Home">
    <div class="relative min-h-screen bg-fixed bg-cover bg-center"
        style="background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('{{ asset('storage/images/background.jpg') }}');">
    {{--  Page Content      --}}
        {{-- Hero --}}
        <x-home-hero />
        {{--  Choose Category  --}}
        <x-category-grid :categories="$categories" />
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-12">
            {{-- Latest Recipes and recent comments --}}
                {{-- Main section with recipes --}}
                <div class="lg:col-span-2">
                    <x-latest-recipes :recipes="$latestRecipes" />
                </div>
                {{-- Sidebar with comments --}}
                <aside>
                    <x-recent-comments :comments="$recentComments" />
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
