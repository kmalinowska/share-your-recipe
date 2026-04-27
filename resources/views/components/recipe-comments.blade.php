@props(['comments', 'recipe'])

<section class="mt-16 bg-base-200/90 backdrop-blur-sm rounded-[2rem] p-6 md:p-10 border border-base-200 shadow-sm">
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-2xl font-black text-base-content flex items-center gap-3">
            <x-heroicon-o-chat-bubble-left-right class="size-7 text-primary" />
            Comments
        </h3>
        <span class="badge badge-primary font-bold">{{ $comments->total() }}</span>
    </div>

    {{-- Comment adding form --}}
    {{-- @include('recipes.partials.comment-form') --}}

    <div class="space-y-6 mt-8">
        @forelse($comments as $comment)
            <div class="bg-base-100/50 p-6 rounded-3xl border border-base-200 shadow-sm transition-all hover:bg-base-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="avatar {{ !$comment->user?->avatar ? 'placeholder' : '' }}">
                            <div class="bg-primary/10 text-primary rounded-full w-10 ring-2 ring-primary/5">
                                @if($comment->user?->avatar)
                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->author_name }}" />
                                @else
                                    <span class="text-sm font-bold uppercase">{{ substr($comment->author_name ?? 'G', 0, 1) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-base tracking-tight text-base-content">
                                {{ $comment->author_name ?? 'Guest User' }}
                            </span>
                            <span class="text-[10px] opacity-50 font-bold uppercase tracking-widest">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Reply Context --}}
                @if($comment->parent)
                    <div class="ml-4 mb-3 px-4 py-2 bg-base-200/50 rounded-2xl border-l-4 border-primary/30">
                        <p class="text-xs opacity-70">
                            <span class="font-bold text-primary">Reply to {{ $comment->parent->author_name }}:</span>
                            <span class="italic">"{{ Str::limit($comment->parent->content, 60) }}"</span>
                        </p>
                    </div>
                @endif

                <p class="text-base-content/80 leading-relaxed pl-1">
                    {{ $comment->content }}
                </p>

                {{-- Optional: Reply button --}}
                <div class="mt-4 flex justify-end">
                    <button class="btn btn-ghost btn-xs gap-2 text-primary/70 hover:text-primary capitalize">
                        <x-heroicon-o-arrow-uturn-left class="size-3" /> Reply
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-base-100/30 rounded-3xl border-2 border-dashed border-base-300">
                <x-heroicon-o-chat-bubble-bottom-center-text class="size-12 mx-auto text-base-content/20 mb-3" />
                <p class="opacity-50 italic">No comments yet. Share your thoughts about this recipe!</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $comments->links() }}
    </div>
</section>
