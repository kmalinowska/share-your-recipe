@props(['comment', 'recipe', 'level' => 0])

<div x-data="{ open: false }"
     class="relative bg-base-100/50 p-6 rounded-3xl border border-base-200 shadow-sm transition-all hover:bg-base-100 text-left"
     style="margin-left: {{ $level * 24 }}px;">

    {{-- TOP: USER --}}
    <div class="flex items-start gap-3">

        {{-- Avatar --}}
        <div class="avatar">
            <div class="w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                {{ substr($comment->author_name ?? 'G', 0, 1) }}
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1">

            <div class="flex items-center justify-between">
                <span class="font-bold text-sm text-base-content">
                    {{ $comment->author_name ?? 'Guest' }}
                </span>

                <span class="text-xs opacity-50">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>

            <p class="mt-2 text-sm text-base-content/80 leading-relaxed">
                {{ $comment->content }}
            </p>

            {{-- Reply button --}}
            <button @click="open = !open"
                    class="mt-3 text-xs text-primary hover:underline">
                Reply
            </button>

            {{-- Reply form --}}
            <div x-show="open" x-cloak class="mt-3">
                <form action="{{ route('comments.store', $recipe) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    @guest
                        <input type="text" name="guest_name"
                               placeholder="Your name"
                               class="input input-sm w-full mb-2" required>
                    @endguest

                    <textarea name="content"
                              class="textarea textarea-sm w-full"
                              placeholder="Write reply..." required></textarea>

                    <button class="btn btn-primary btn-xs mt-2">
                        Send
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- REPLIES (IMPORTANT UX INDENT) --}}
    @if($comment->replies->count())
        <div class="mt-5 space-y-4 border-l-2 border-primary/10 pl-4">

            @foreach($comment->replies as $reply)
                <x-comment-item
                    :comment="$reply"
                    :recipe="$recipe"
                    :level="$level + 1" />
            @endforeach

        </div>
    @endif

</div>
