@props(['recipe', 'tags'])

<div class="space-y-6">
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

    <div class="pt-2">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_commentable" value="1" {{ old('is_commentable', $recipe->is_commentable) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-600 font-medium">Allow comments on this recipe</span>
        </label>
    </div>
</div>
