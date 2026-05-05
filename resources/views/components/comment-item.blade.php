@props(['comment', 'recipe', 'depth' => 0])
{{-- Flat threading: comments have depth 0, replies have depth 1. --}}
{{-- Replying to a reply always goes back to depth 1 (same thread). --}}

<div x-data="{ showReplies: false, showForm: false }"
     class="bg-base-100/70 p-5 rounded-3xl border border-base-200 shadow-sm">

    {{-- TOP --}}
    <div class="flex gap-3">

        {{-- AVATAR --}}
        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                {{ substr($comment->author_name ?? 'G', 0, 1) }}
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="flex-1 min-w-0">

            {{-- Author + date --}}
            <div class="flex justify-between items-center gap-2">
                <span class="font-bold text-sm truncate">
                    {{ $comment->author_name }}
                </span>
                <span class="text-xs opacity-50 whitespace-nowrap flex-shrink-0">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>

            {{-- Comment text --}}
            <p class="mt-2 text-sm text-base-content/80 break-words leading-relaxed">
                {{ $comment->content }}
            </p>

            {{-- ACTIONS --}}
            <div class="flex gap-4 mt-3 text-xs flex-wrap items-center">

                {{-- Reply button — visible at all depths --}}
                {{-- At depth 1, the reply goes to the root comment (flat) --}}
                <button @click="showForm = !showForm"
                        class="text-primary hover:underline font-medium">
                    <span x-show="!showForm">Reply</span>
                    <span x-show="showForm" x-cloak>Cancel</span>
                </button>

                {{-- Toggle replies — only on root comments (depth 0) --}}
                @if($depth === 0 && $comment->replies->isNotEmpty())
                    <button @click="showReplies = !showReplies"
                            class="text-primary font-bold hover:underline">
                        <span x-show="!showReplies">
                            View replies {{ $comment->replies->count() }}
                            {{ Str::plural('reply', $comment->replies->count()) }}
                        </span>
                        <span x-show="showReplies" x-cloak>
                            Hide replies
                        </span>
                    </button>
                @endif

            </div>

            {{-- REPLY FORM --}}
            <div x-show="showForm" x-cloak x-transition class="mt-3">
                <form action="{{ route('comments.store', $recipe) }}" method="POST" class="space-y-2">
                    @csrf
                    {{-- Flat threading:
                         - depth 0 → parent_id = this comment's id
                         - depth 1 → parent_id = this comment's id (which is already a reply)
                           The controller will resolve it to the root comment.
                    --}}
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    @guest
                        <input type="text"
                               name="guest_name"
                               class="input input-sm input-bordered w-full rounded-xl"
                               placeholder="Your name"
                               required>
                    @endguest

                    <div class="flex gap-2 items-end">
                        <textarea name="content"
                                  class="textarea textarea-sm textarea-bordered w-full rounded-xl"
                                  rows="2"
                                  placeholder="Write a reply..."
                                  required></textarea>

                        <button type="submit"
                                class="btn btn-primary btn-sm flex-shrink-0">
                            Send
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- REPLIES — flat list, shown only on root comments (depth 0) --}}
    @if($depth === 0 && $comment->replies->isNotEmpty())
        <div x-show="showReplies"
             x-transition
             x-cloak
             class="mt-4 ml-6 pl-4 border-l-2 border-primary/20 space-y-3">

            @foreach($comment->replies as $reply)
                {{-- Depth 1 — no further nesting rendered --}}
                <x-comment-item
                    :comment="$reply"
                    :recipe="$recipe"
                    :depth="1"
                />
            @endforeach

        </div>
    @endif

</div>
