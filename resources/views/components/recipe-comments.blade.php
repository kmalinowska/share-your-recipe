@props(['comments', 'recipe'])

<section class="mt-16 bg-base-200/90 backdrop-blur-sm rounded-[2rem] p-6 md:p-10 border border-base-200 shadow-sm">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-2xl font-black text-base-content flex items-center gap-3">
            <x-heroicon-o-chat-bubble-left-right class="size-7 text-primary" />
            Comments
        </h3>
        <span class="badge badge-primary font-bold">{{ $comments->total() }}</span>
    </div>

    <div class="space-y-6 mt-8">
        @forelse($comments as $comment)
            <div x-data="{ localReply: false }" class="bg-base-100/50 p-6 rounded-3xl border border-base-200 shadow-sm transition-all hover:bg-base-100 text-left">
                {{-- Comment content (Avatar, Name, Date) --}}
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

                {{-- Replies --}}
                @if($comment->replies->count())
                    <div class="mt-6 ml-6 md:ml-12 space-y-4 border-l-2 border-primary/10 pl-4">
                        @foreach($comment->replies as $reply)
                            <div class="bg-base-100/70 p-4 rounded-2xl border border-base-200 shadow-sm hover:bg-base-100 transition">
                                <div class="flex items-center gap-2 mb-2 text-xs opacity-70">
                    <span class="font-bold text-base-content">
                        {{ $reply->author_name ?? 'Guest User' }}
                    </span>
                                    <span class="opacity-40">
                        {{ $reply->created_at->diffForHumans() }}
                    </span>
                                </div>

                                <p class="text-sm text-base-content/80 leading-relaxed">
                                    {{ $reply->content }}
                                </p>

                            </div>
                        @endforeach

                    </div>
                @endif

                {{-- Reply button --}}
                <div class="mt-4 flex justify-end">
                    <button @click="localReply = !localReply"
                            class="btn btn-ghost btn-xs gap-2 text-primary/70 hover:text-primary capitalize transition-colors">
                        <x-heroicon-o-arrow-uturn-left class="size-3" />
                        <span x-text="localReply ? 'Cancel' : 'Reply'"></span>
                    </button>
                </div>

                {{-- Reply form--}}
                <div x-show="localReply"
                     x-transition
                     x-cloak
                     class="mt-4 ml-8 md:ml-16 p-4 bg-base-200/50 rounded-2xl border border-base-300 shadow-inner">

                    <form action="{{ route('comments.store', $recipe) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                        <div class="space-y-3">
                            @guest
                                <input type="text" name="guest_name" placeholder="Your Name"
                                       class="input input-sm input-bordered rounded-xl bg-base-100 w-full max-w-[180px] text-xs" required>
                            @endguest

                            <div class="relative flex items-center">
                        <textarea name="content"
                                  class="textarea textarea-bordered rounded-xl bg-base-100 w-full pr-12 focus:bg-white transition-all h-20 min-h-[5rem] text-sm pt-3"
                                  placeholder="Write your reply here..." required></textarea>

                                {{-- Submit --}}
                                <button type="submit"
                                        class="absolute bottom-2 right-2 btn btn-circle btn-primary btn-sm shadow-md hover:scale-105 transition-transform">
                                    <x-heroicon-o-paper-airplane class="size-4 rotate-90" />
                                </button>
                            </div>
                        </div>
                    </form>
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

    {{-- Create comments --}}
    <div class="bg-base-200/50 p-8 md:p-12 rounded-[3rem] border border-base-300 shadow-sm overflow-hidden">
        <div class="max-w-2xl mx-auto text-center"> {{-- Wewnętrzny kontener dla formularza --}}
            <h3 class="text-3xl font-black mb-2">Leave a <span class="text-primary">Comment</span></h3>
            <p class="text-base-content/60 mb-8 text-sm uppercase tracking-widest font-bold">We'd love to hear from you</p>

            <form action="{{ route('comments.store', $recipe) }}" method="POST" class="space-y-6">
                @csrf

                @guest
                    <div class="form-control w-full">
                        <input type="text" name="guest_name" placeholder="Enter your name"
                               class="input input-bordered input-lg rounded-2xl bg-base-100 w-full focus:ring-2 focus:ring-primary/20 transition-all text-center" required>
                    </div>
                @endguest

                <div class="form-control w-full relative" x-data="{ content: '', max: 1000 }">
                    <textarea name="content"
                              x-model="content"
                              maxlength="1000"
                              class="textarea textarea-bordered rounded-[2.5rem] bg-base-100 w-full h-48 p-8 text-lg focus:ring-2 focus:ring-primary/20 transition-all leading-relaxed"
                              placeholder="Your message goes here..." required></textarea>

                    {{-- Licznik znaków --}}
                    <div class="absolute bottom-6 right-8 text-[11px] font-mono font-black opacity-30">
                        <span x-text="content.length"></span> / <span x-text="max"></span>
                    </div>
                </div>

                <div class="flex justify-center pt-2">
                    <button type="submit" class="btn btn-primary btn-xl rounded-2xl px-12 h-16 shadow-xl shadow-primary/20 gap-3 group">
                        <span class="text-lg font-black uppercase tracking-tight">Post Comment</span>
                        <x-heroicon-o-paper-airplane class="size-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
