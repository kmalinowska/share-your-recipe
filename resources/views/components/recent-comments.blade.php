@props(['comments'])

<aside class="py-8">
    <div class="flex items-center gap-2 mb-8">
        <h2 class="text-2xl font-black italic">Recent Activity</h2>
        <span class="badge badge-ghost badge-sm opacity-50">{{ $comments->count() }}</span>
    </div>

    <div class="bg-base-100 p-6 rounded-[2rem] border border-base-300 shadow-sm">
        @forelse($comments as $comment)
            <div class="group mb-6 last:mb-0 pb-6 last:pb-0 border-b last:border-0 border-base-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="avatar {{ !$comment->user?->avatar ? 'placeholder' : '' }}">
                            <div class="bg-primary/10 text-primary rounded-full w-8 ring-1 ring-primary/10">
                                @if($comment->user?->avatar)
                                    {{-- Displays the photo if it exists --}}
                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->author_name }}" />
                                @else
                                    {{-- Displays initial if no photo or guest --}}
                                    <span class="text-xs font-bold uppercase">
                                        {{ substr($comment->author_name ?? 'G', 0, 1) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="font-bold text-sm tracking-tight">
                            {{ $comment->author_name ?? 'Guest User' }}</span>
                    </div>

                    {{-- Add date --}}
                    <span class="text-[10px] opacity-40 font-medium uppercase tracking-tighter">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>

                    {{--  Parent  --}}
                @if($comment->parent)
                    <div class="ml-11 mb-2 px-2 py-1 bg-base-200/50 rounded-lg border-l-2 border-primary/30">
                        <p class="text-[10px] opacity-60 leading-tight">
                            <span class="font-bold text-primary/70">Reply to {{ $comment->parent->author_name }}:</span>
                            <span class="line-clamp-1 italic">"{{ $comment->parent->content }}"</span>
                        </p>
                    </div>
                @endif

                <p class="text-sm opacity-75 italic line-clamp-2 leading-relaxed px-1">
                    "{{ $comment->content }}"
                </p>

                @if($comment->recipe)
                <a href="{{ route('recipes.show', $comment->recipe) }}"
                   class="text-[10px] font-black uppercase text-secondary hover:text-primary mt-3 inline-flex items-center gap-1 transition-colors tracking-tight">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ $comment->recipe->title }}
                </a>
                @endif
            </div>
        @empty
            <div class="text-center py-10">
                <p class="opacity-40 italic text-sm">No comments yet. Be the first to join the talk!</p>
            </div>
        @endforelse
    </div>
</aside>
