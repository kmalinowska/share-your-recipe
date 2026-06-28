<div class="bg-base-100 shadow-xl rounded-2xl p-6 md:p-10 space-y-4">

    <div class="border-b border-base-200 pb-2">
        <h2 class="text-xl font-bold text-base-content/80">Preparation Steps</h2>
    </div>

    <div class="space-y-4">
        <template x-for="(step, index) in steps" :key="index">
            <div class="flex gap-4 items-start bg-base-200/30 p-4 rounded-xl border border-base-200">

                {{-- Step number --}}
                <div class="flex-shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold text-sm sm:text-base shadow-sm mt-1">
                    <span x-text="index + 1"></span>
                </div>

                {{-- Textarea --}}
                <div class="flex-1 form-control">
                    <textarea
                        :name="`steps[${index}]`"
                        x-model="steps[index]"
                        rows="2"
                        placeholder="Describe this step..."
                        class="textarea textarea-bordered w-full text-base focus:textarea-primary"
                        required
                    ></textarea>
                </div>

                {{-- Remove button --}}
                <button
                    type="button"
                    @click="steps = steps.filter((_, i) => i !== index)"
                    :disabled="steps.length === 1"
                    class="btn btn-ghost btn-sm text-error btn-circle flex-shrink-0 mt-1"
                    title="Remove step">
                    ✕
                </button>

            </div>
        </template>
    </div>

    @error('steps')
    <p class="text-error text-xs mt-1">{{ $message }}</p>
    @enderror
    @error('steps.*')
    <p class="text-error text-xs mt-1">{{ $message }}</p>
    @enderror

    {{-- Add step button --}}
    <button
        type="button"
        @click="steps.push('')"
        class="btn btn-outline btn-sm btn-primary mt-2 w-full sm:w-48">
        + Add Step
    </button>

</div>
