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
            <x-comment-item :comment="$comment" :recipe="$recipe" />
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
