<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User dashboard & Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8 space-y-6 lg:space-y-0">

                <div class="space-y-6 lg:col-span-1">
                    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">

                    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('My recipes') }} ({{ $recipes->count() }})
                            </h3>
                            <a href="{{ route('recipes.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('+ Add recipe') }}
                            </a>
                        </div>

                        @if($recipes->isEmpty())
                            <p class="text-sm text-gray-600 dark:text-gray-400">You haven't added any recipes yet.</p>
                        @else
                            <div class="overflow-hidden border border-gray-200 dark:border-gray-700 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tytuł</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Akcje</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recipes as $recipe)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('recipes.show', $recipe->slug) }}" class="hover:underline text-blue-600 dark:text-blue-400">
                                                    {{ $recipe->title }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-2">
                                                <a href="{{ route('recipes.edit', $recipe->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Edit
                                                </a>

                                                <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this recipe?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
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
                    </div>

                    <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('My recipe comments') }} ({{ $comments->count() }})
                        </h3>

                        @if($comments->isEmpty())
                            <p class="text-sm text-gray-600 dark:text-gray-400">You haven't written any comments yet.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($comments as $comment)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex justify-between items-start">
                                        <div class="space-y-1 max-w-[85%]">
                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                To recipe:
                                                @if($comment->recipe)
                                                    <a href="{{ route('recipes.show', $comment->recipe->slug) }}" class="hover:underline text-blue-500">
                                                        {{ $comment->recipe->title }}
                                                    </a>
                                                @else
                                                    <span class="italic text-gray-400">Removed recipe</span>
                                                @endif
                                                • {{ $comment->created_at->diffForHumans() }}
                                            </p>
                                            <p class="text-sm text-gray-800 dark:text-gray-200 break-words">
                                                "{{ $comment->content }}"
                                            </p>
                                        </div>
                                        <div>
                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Usunąć ten komentarz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
