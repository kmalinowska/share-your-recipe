<section>
    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100 text-gray-900">
        <x-heroicon-o-chat-bubble-left-right class="size-5 text-indigo-600" />
        <h3 class="text-lg font-bold tracking-tight">
            {{ __('My recipe comments') }} ({{ $comments->count() }})
        </h3>
    </div>

    @if($comments->isEmpty())
        <p class="text-sm text-gray-600">You haven't written any comments yet.</p>
    @else
        <div class="space-y-4">
            @foreach($comments as $comment)
                <div class="p-4 bg-base-100 border border-base-200 rounded-lg flex justify-between items-start">
                    <div class="space-y-1 max-w-[85%]">
                        <p class="text-xs font-semibold text-gray-500">
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
                        <p class="text-sm text-gray-800 break-words">
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
</section>
