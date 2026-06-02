@props(['recipe', 'categories'])

<div class="space-y-6">
    <div>
        <div class="flex items-center gap-2 mb-2">
            <x-heroicon-o-pencil-square class="size-5 text-indigo-600" />
            <label for="title" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Recipe Title') }}</label>
        </div>
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full bg-base-50" :value="old('title', $recipe->title)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <x-heroicon-o-clock class="size-5 text-indigo-600" />
                <label for="preparation_time" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Preparation Time (in minutes)') }}</label>
            </div>
            <x-text-input id="preparation_time" name="preparation_time" type="number" class="mt-1 block w-full bg-base-50" :value="old('preparation_time', $recipe->preparation_time)" required />
            <x-input-error class="mt-2" :messages="$errors->get('preparation_time')" />
        </div>

        <div>
            <div class="flex items-center gap-2 mb-2">
                <x-heroicon-o-folder class="size-5 text-indigo-600" />
                <label for="category_id" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Category') }}</label>
            </div>
            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-base-50" required>
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
</div>
