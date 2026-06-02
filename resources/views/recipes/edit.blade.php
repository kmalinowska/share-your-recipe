<x-app-layout title="Edit Recipe">
    <div class="min-h-screen bg-cover bg-center bg-no-repeat bg-fixed py-8 relative"
         style="background-image:
         linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
         url('{{ asset('storage/images/recipe/recipe-background7.jpg') }}')"
         x-data="{
            ingredients: @js(
                old('ingredients')
                    ? array_values(old('ingredients'))
                    : $recipe->ingredients->map(fn($i) => ['name' => $i->name, 'quantity' => $i->pivot->quantity, 'unit' => $i->pivot->unit])->toArray()
            ),
            steps: @js(
                old('steps')
                    ? collect(old('steps'))->map(fn($s) => ['value' => $s])->values()->toArray()
                    : collect($recipe->preparation ?? [])->map(fn($s) => ['value' => $s])->toArray()
            )
         }">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 relative z-10">

            {{-- Back button --}}
            <div class="mb-6">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium transition bg-white/80 backdrop-blur-sm px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Back to Dashboard
                </a>
            </div>

            {{-- Page Header --}}
            <div class="text-center mb-10 bg-white/60 backdrop-blur-md py-4 px-6 rounded-2xl max-w-xl mx-auto shadow-sm border border-gray-200/50">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    {{ __('Edit Recipe:') }} <span class="text-indigo-600">{{ $recipe->title }}</span>
                </h1>
            </div>

            <div class="bg-white overflow-visible shadow-md sm:rounded-2xl p-6 border border-gray-100">
                <form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- 1. Basic Info Component --}}
                    @include('components.recipe-form.edit.basic-info', ['recipe' => $recipe, 'categories' => $categories])

                    {{-- 2. Image Upload Component --}}
                    @include('components.recipe-form.edit.image-upload', ['recipe' => $recipe])

                    {{-- 3. Ingredients Component --}}
                    @include('components.recipe-form.edit.ingredients')

                    {{-- 4. Steps Component --}}
                    @include('components.recipe-form.edit.steps')

                    {{-- 5. Tags & Options Component --}}
                    @include('components.recipe-form.edit.tags', ['recipe' => $recipe, 'tags' => $tags])

                    {{-- Sticky/Fixed Save Button (Mobile & Desktop UX) --}}
                    <div class="fixed bottom-0 left-0 right-0 lg:static bg-white/95 backdrop-blur-md lg:bg-transparent border-t border-gray-100 lg:border-none p-4 lg:p-0 z-50">
                        <div class="max-w-4xl w-full mx-auto px-4 lg:px-0 flex flex-row items-center justify-center gap-3">
                            <a href="{{ route('profile.edit') }}"
                               class="inline-flex justify-center items-center px-4 py-3 border border-gray-200 rounded-xl font-semibold text-base text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition min-w-[100px] sm:min-w-[120px] text-center">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="flex-1 lg:flex-none inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-base text-white shadow-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition sm:px-12">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

