<div class="bg-base-100 shadow-xl rounded-2xl p-6 md:p-10 space-y-4">
    <div class="border-b border-base-200 pb-2">
        <h2 class="text-xl font-bold text-base-content/80">Tags</h2>
    </div>

    {{-- Wyświetlanie wybranych tagów jako Badges z "x" --}}
    <div class="flex flex-wrap gap-2 min-h-[48px] p-3 bg-base-200/40 rounded-xl items-center border border-dashed border-base-300">
        <template x-if="tags.length === 0">
            <span class="text-sm text-base-content/40 pl-2">No tags selected. Add using the selection below.</span>
        </template>

        <template x-for="(tagId, index) in tags" :key="tagId">
            <div class="badge badge-primary badge-lg gap-2 pr-1 pl-3 py-3 font-medium">
                <span x-text="availableTags.find(t => t.id === tagId)?.name"></span>
                <button type="button" @click="tags.splice(index, 1)" class="hover:bg-primary-focus rounded-full p-0.5 text-xs transition">✕</button>
                {{-- Ukryty input dla Laravela --}}
                <input type="hidden" name="tags[]" :value="tagId">
            </div>
        </template>
    </div>

    {{-- Dodawanie tagu przez wybór/kliknięcie --}}
    <div class="flex flex-wrap gap-2 p-3 bg-base-200/20 border border-base-200 rounded-xl max-h-32 overflow-y-auto">
        <template x-for="tag in availableTags" :key="tag.id">
            <button type="button"
                    @click="if(!tags.includes(tag.id)) tags.push(tag.id)"
                    :class="tags.includes(tag.id) ? 'btn-disabled opacity-40' : ''"
                    class="btn btn-xs btn-outline">
                + <span x-text="tag.name"></span>
            </button>
        </template>
    </div>
    @error('tags') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
</div>
