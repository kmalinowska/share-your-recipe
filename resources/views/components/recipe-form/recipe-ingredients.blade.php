<div class="bg-base-100 shadow-xl rounded-2xl p-6 md:p-10 space-y-4">
    <div class="border-b border-base-200 pb-2">
        <h2 class="text-xl font-bold text-base-content/80">Ingredients</h2>
    </div>

    {{-- Mobilny Layout (Ingredient Cards) / Desktop Layout (Table-like rows) --}}
    <div class="space-y-3">
        <template x-for="(ingredient, index) in ingredients" :key="index">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center bg-base-200/50 lg:bg-transparent p-4 lg:p-0 rounded-xl relative border border-base-200 lg:border-none">

                {{-- Name --}}
                <div class="md:col-span-6 form-control">
                    <label class="label lg:hidden"><span class="label-text-alt font-bold">Ingredient Name</span></label>
                    <input type="text" :name="`ingredients[${index}][name]`" x-model="ingredient.name" placeholder="e.g. Flour" class="input input-bordered input-md lg:input-sm w-full" required />
                </div>

                {{-- Quantity --}}
                <div class="md:col-span-3 form-control">
                    <label class="label lg:hidden"><span class="label-text-alt font-bold">Quantity</span></label>
                    <input type="text" :name="`ingredients[${index}][quantity]`" x-model="ingredient.quantity" placeholder="e.g. 250" class="input input-bordered input-md lg:input-sm w-full" required />
                </div>

                {{-- Unit --}}
                <div class="md:col-span-2 form-control">
                    <label class="label lg:hidden"><span class="label-text-alt font-bold">Unit</span></label>
                    <input type="text" :name="`ingredients[${index}][unit]`" x-model="ingredient.unit" placeholder="e.g. g" class="input input-bordered input-md lg:input-sm w-full" required />
                </div>

                {{-- Remove Button --}}
                <div class="md:col-span-1 flex justify-end pt-4 lg:pt-0">
                    <button type="button" @click="ingredients.splice(index, 1)" :disabled="ingredients.length === 1" class="btn btn-error btn-sm btn-outline lg:btn-ghost text-error w-full lg:w-auto">
                        <span class="lg:hidden">Remove Ingredient</span>
                        <span class="hidden lg:inline">✕</span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    @error('ingredients') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror

    <button type="button" @click="ingredients.push({ name: '', quantity: '', unit: '' })" class="btn btn-outline btn-sm btn-primary mt-3 w-full sm:w-48">
        + Add Ingredient
    </button>
</div>
