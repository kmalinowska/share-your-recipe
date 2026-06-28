<x-app-layout title="Create New Recipe">
    {{-- Poprawiony główny div: dodaliśmy inline style z background-image oraz odpowiednie klasy pozycjonujące --}}
    <div class="min-h-screen bg-cover bg-center bg-no-repeat bg-fixed py-8 relative"
         style="background-image:
         linear-gradient(rgba(255,255,255,0.82), rgba(255,255,255,0.82)),
         url('{{ Vite::asset('resources/images/backgrounds/recipe-background.jpg') }}')"

         x-data="recipeForm(
        {{ json_encode(old('ingredients', [['name' => '', 'quantity' => '', 'unit' => '']])) }},
        {{ json_encode(old('steps', [''])) }},
        {{ json_encode(old('tags', [])) }},
        {{ json_encode($tags) }}
        )"
    >

        {{-- Wewnętrzny kontener trzymający strukturę strony --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 relative z-10">

            {{-- Back to Home --}}
            <div class="mb-6">
                {{-- Dodany mały backdrop-blur/bg-base-100, żeby link powrotu był czytelny na każdym tle --}}
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-primary hover:text-secondary font-medium transition bg-base-100/80 backdrop-blur-sm px-4 py-2 rounded-xl shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Back to Home
                </a>
            </div>

            {{-- Nagłówek strony owinięty w lekki panel dla czytelności --}}
            <div class="text-center mb-10 bg-base-100/60 backdrop-blur-md py-4 rounded-2xl max-w-md mx-auto shadow-sm border border-base-200/50">
                <h1 class="text-4xl font-extrabold text-base-content tracking-tight">Create New Recipe</h1>
            </div>

            <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- 1. Basic Info Component --}}
                <x-recipe-form.recipe-basic-info :categories="$categories" />

                {{-- 2. Ingredients Component --}}
                <x-recipe-form.recipe-ingredients />

                {{-- 3. Steps Component --}}
                <x-recipe-form.recipe-steps />

                {{-- 4. Tags Component --}}
                <x-recipe-form.recipe-tags />

                {{-- 5. Image Upload Component --}}
                <x-recipe-form.recipe-image-upload />

                {{-- Sticky Save Button (Mobile & Desktop UX) --}}
                <div class="fixed bottom-0 left-0 right-0 lg:static bg-base-100/95 backdrop-blur-md lg:bg-transparent border-t border-base-200 lg:border-none p-4 lg:p-0 z-50">
                    <div class="max-w-4xl w-full mx-auto px-4 lg:px-0 flex flex-row items-center justify-center gap-3">
                        <a href="{{ route('home') }}"
                           class="btn btn-outline border-base-300 rounded-xl font-semibold text-base min-w-[100px] sm:min-w-[120px]">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary flex-1 lg:flex-none lg:btn-wide text-lg font-bold shadow-lg rounded-xl">
                            Save Recipe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
