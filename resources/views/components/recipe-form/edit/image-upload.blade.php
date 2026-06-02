@props(['recipe'])

<div>
    <div class="flex items-center gap-2 mb-2">
        <x-heroicon-o-photo class="size-5 text-indigo-600" />
        <label for="image_path" class="text-base font-bold text-gray-900 tracking-tight">{{ __('Recipe Image (leave empty to keep current)') }}</label>
    </div>
    @if($recipe->image_path)
        <div class="my-3">
            <img src="{{ asset($recipe->image_path) }}" alt="{{ $recipe->title }}" class="h-96 w-auto rounded-lg shadow-sm object-cover border border-gray-200">
        </div>
    @endif
    <input id="image_path" name="image_path" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-base-50 focus:outline-none" />
    <x-input-error class="mt-2" :messages="$errors->get('image_path')" />
</div>
