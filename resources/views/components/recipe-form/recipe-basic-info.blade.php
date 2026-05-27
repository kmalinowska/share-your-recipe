@props(['categories'])
<div class="bg-base-100 shadow-xl rounded-2xl p-6 md:p-10 space-y-6">
    <div class="border-b border-base-200 pb-2">
        <h2 class="text-xl font-bold text-base-content/80">Recipe Info</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Recipe Name --}}
        <div class="form-control w-full">
            <label class="label font-semibold" for="title"><span class="label-text">Recipe name</span></label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Enter recipe name..." class="input input-bordered w-full @error('title') input-error @enderror" required />
            @error('title') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Author --}}
        <div class="form-control w-full">
            <label class="label font-semibold"><span class="label-text">Author</span></label>
            <input type="text" value="{{ auth()->user()->name }}" class="input input-bordered w-full bg-base-200 cursor-not-allowed" readonly />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Time --}}
        <div class="form-control w-full">
            <label class="label font-semibold" for="preparation_time"><span class="label-text">Time (min.)</span></label>
            <input type="number" id="preparation_time" name="preparation_time" value="{{ old('preparation_time') }}" placeholder="e.g. 45" min="1" class="input input-bordered w-full @error('preparation_time') input-error @enderror" required />
            @error('preparation_time') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Category --}}
        <div class="form-control w-full">
            <label class="label font-semibold" for="category_id"><span class="label-text">Category</span></label>
            <select id="category_id" name="category_id" class="select select-bordered w-full @error('category_id') select-error @enderror" required>
                <option value="" disabled selected>Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
