<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Recipe:') }} {{ $recipe->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('recipes.update', $recipe->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" :value="__('Recipe Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $recipe->title)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="preparation_time" :value="__('Preparation Time (in minutes)')" />
                            <x-text-input id="preparation_time" name="preparation_time" type="number" class="mt-1 block w-full" :value="old('preparation_time', $recipe->preparation_time)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('preparation_time')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
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
                        <x-input-label for="image_path" :value="__('Recipe Image (leave empty to keep current)')" />
                        @if($recipe->image_path)
                            <div class="my-2">
                                <img src="{{ asset('storage/' . $recipe->image_path) }}" alt="{{ $recipe->title }}" class="h-32 w-auto rounded shadow-sm object-cover">
                            </div>
                        @endif
                        <input id="image_path" name="image_path" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" />
                        <x-input-error class="mt-2" :messages="$errors->get('image_path')" />
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_commentable" value="1" {{ old('is_commentable', $recipe->is_commentable) ? 'checked' : '' }} class="rounded dark:bg-gray-900 border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Allow comments on this recipe</span>
                        </label>
                    </div>

                    <div x-data="{
                        ingredients: @js(is_string(old('ingredients', $recipe->ingredients)) ? json_decode(old('ingredients', $recipe->ingredients), true) : old('ingredients', $recipe->ingredients->map(fn($i) => ['name' => $i->name, 'quantity' => $i->pivot->quantity, 'unit' => $i->pivot->unit])))
                    }" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Ingredients</h3>

                        <div class="space-y-2">
                            <template x-for="(ingredient, index) in ingredients" :key="index">
                                <div class="flex gap-2 items-center">
                                    <input type="text" :name="`ingredients[${index}][name]`" x-model="ingredient.name" placeholder="Name (e.g., Flour)" class="w-1/2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                    <input type="text" :name="`ingredients[${index}][quantity]`" x-model="ingredient.quantity" placeholder="Quantity (e.g., 200)" class="w-1/4 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                    <input type="text" :name="`ingredients[${index}][unit]`" x-model="ingredient.unit" placeholder="Unit (e.g., g)" class="w-1/4 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>

                                    <button type="button" @click="ingredients.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold px-2">X</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="ingredients.push({name: '', quantity: '', unit: ''})" class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            + Add another ingredient
                        </button>
                    </div>

                    <div x-data="{
                        steps: @js(is_string(old('steps', $recipe->preparation)) ? json_decode(old('steps', $recipe->preparation), true) : old('steps', $recipe->preparation ?? []))
                    }" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Preparation Steps</h3>

                        <div class="space-y-2">
                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex gap-2 items-start">
                                    <span class="mt-2 font-bold text-gray-500" x-text="`${index + 1}.`"></span>
                                    <textarea :name="`steps[${index}]`" x-model="steps[index]" rows="2" placeholder="Describe this step..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required></textarea>

                                    <button type="button" @click="steps.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold px-2 mt-2">X</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="steps.push('')" class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            + Add another step
                        </button>
                    </div>

                    <div>
                        <x-input-label :value="__('Tags')" class="mb-2" />
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($tags as $tag)
                                <label class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                           {{ in_array($tag->id, old('tags', $recipe->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="rounded dark:bg-gray-900 border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
