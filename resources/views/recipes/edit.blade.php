<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Recipe:') }} {{ $recipe->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6 border border-base-200">

                <form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-pencil-square class="size-5 text-indigo-600" />
                            <label for="title" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Recipe Title') }}</label>
                        </div>
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $recipe->title)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <x-heroicon-o-clock class="size-5 text-indigo-600" />
                                <label for="preparation_time" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Preparation Time (in minutes)') }}</label>
                            </div>
                            <x-text-input id="preparation_time" name="preparation_time" type="number" class="mt-1 block w-full" :value="old('preparation_time', $recipe->preparation_time)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('preparation_time')" />
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <x-heroicon-o-folder class="size-5 text-indigo-600" />
                                <label for="category_id" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Category') }}</label>
                            </div>
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $recipe->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-photo class="size-5 text-indigo-600" />
                            <label for="image_path" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Recipe Image (leave empty to keep current)') }}</label>
                        </div>
                        @if($recipe->image_path)
                            <div class="my-2">
                                <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->title }}" class="h-96 w-auto rounded shadow-sm object-cover">
                            </div>
                        @endif
                        <input id="image_path" name="image_path" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-base-100 focus:outline-none" />
                        <x-input-error class="mt-2" :messages="$errors->get('image_path')" />
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_commentable" value="1" {{ old('is_commentable', $recipe->is_commentable) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600">Allow comments on this recipe</span>
                        </label>
                    </div>

                    <div x-data="{
                        ingredients: @js(
                            old('ingredients')
                                ? array_values(old('ingredients'))
                                : $recipe->ingredients->map(fn($i) => ['name' => $i->name, 'quantity' => $i->pivot->quantity, 'unit' => $i->pivot->unit])->toArray()
                        )
                    }" class="p-4 bg-base-100 border border-base-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                            <x-heroicon-o-clipboard-document-list class="size-5 text-indigo-600" />
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Ingredients</h3>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(ingredient, index) in ingredients" :key="index">
                                <div class="flex gap-2 items-center">
                                    <input type="text" :name="`ingredients[${index}][name]`" x-model="ingredient.name" placeholder="Name (e.g., Flour)" class="w-1/2 rounded-md border-gray-300" required>
                                    <input type="text" :name="`ingredients[${index}][quantity]`" x-model="ingredient.quantity" placeholder="Quantity (e.g., 200)" class="w-1/4 rounded-md border-gray-300" required>
                                    <input type="text" :name="`ingredients[${index}][unit]`" x-model="ingredient.unit" placeholder="Unit (e.g., g)" class="w-1/4 rounded-md border-gray-300" required>

                                    <button type="button" @click="ingredients.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold px-2">X</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="ingredients.push({name: '', quantity: '', unit: ''})" class="mt-3 text-sm text-indigo-600 hover:underline flex items-center gap-1">
                            + Add another ingredient
                        </button>
                    </div>

                    <div x-data="{
                        steps: @js(
                            old('steps')
                                ? collect(old('steps'))->map(fn($s) => ['value' => $s])->values()->toArray()
                                : collect($recipe->preparation ?? [])->map(fn($s) => ['value' => $s])->toArray()
                        )
                    }" class="p-4 bg-base-100 border border-base-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                            <x-heroicon-o-list-bullet class="size-5 text-indigo-600" />
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Preparation Steps</h3>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex gap-2 items-baseline">
                                    <span class="font-bold text-gray-500 min-w-[24px] text-right select-none" x-text="`${index + 1}.`"></span>

                                    <textarea :name="`steps[${index}]`" x-model="step.value" rows="2" placeholder="Describe this step..." class="w-full rounded-md border-gray-300" required></textarea>

                                    <button type="button" @click="steps.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold px-2 self-center">X</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="steps.push({value: ''})" class="mt-3 text-sm text-indigo-600 hover:underline flex items-center gap-1">
                            + Add another step
                        </button>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <x-heroicon-o-tag class="size-5 text-indigo-600" />
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Tags</h3>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($tags as $tag)
                                <label class="inline-flex items-center text-sm text-gray-600">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                           {{ in_array($tag->id, old('tags', $recipe->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:underline">
                            Cancel
                        </a>
                        <div class="flex-none">
                            <x-primary-button type="submit">
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

