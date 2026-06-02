<div class="p-4 bg-base-100 border border-base-200 rounded-lg">
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
