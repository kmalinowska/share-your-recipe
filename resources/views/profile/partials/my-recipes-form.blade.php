<section>
    <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
        <div class="flex items-center gap-2 text-gray-900">
            <x-heroicon-o-book-open class="size-5 text-indigo-600" />
            <h3 class="text-lg font-bold tracking-tight">
                {{ __('My recipes') }} ({{ $recipes->count() }})
            </h3>
        </div>
        <a href="{{ route('recipes.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('+ Add recipe') }}
        </a>
    </div>

    @if($recipes->isEmpty())
        <p class="text-sm text-gray-600">You haven't added any recipes yet.</p>
    @else
        <div class="overflow-hidden border border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recipes as $recipe)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="{{ route('recipes.show', $recipe->slug) }}" class="hover:underline text-blue-600">
                                {{ $recipe->title }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                            <a href="{{ route('recipes.edit', $recipe->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Edit
                            </a>

                            <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this recipe?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
