<div class="p-4 bg-base-100 border border-base-200 rounded-lg">
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
