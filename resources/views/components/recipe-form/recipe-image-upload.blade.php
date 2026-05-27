<div class="bg-base-100 shadow-xl rounded-2xl p-6 md:p-10 space-y-4">
    <div class="border-b border-base-200 pb-2">
        <h2 class="text-xl font-bold text-base-content/80">Photos</h2>
    </div>

    <div class="form-control w-full">
        <label class="label font-semibold" for="image_path"><span class="label-text">Choose Cover Image</span></label>
        <input type="file"
               id="image_path"
               name="image_path"
               accept="image/*"
               class="file-input file-input-bordered file-input-primary w-full @error('image_path') file-input-error @enderror" />
        @error('image_path') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
