@props(['categories'])

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Heading --}}
        <div class="flex flex-col items-center mb-12">
            <h2 class="text-3xl font-black italic text-base-content tracking-tight uppercase">Choose Category</h2>
            <div class="h-1.5 w-16 bg-primary rounded-full mt-2 shadow-sm"></div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('recipes.category', $category->slug) }}"
                   class="group relative aspect-video overflow-hidden rounded-[2.5rem] shadow-lg hover:shadow-2xl transition-all duration-500 border border-base-200">

                    {{-- Photos --}}
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"
                         style="background-image: url('{{ asset('storage/images/categories/' . $category->slug . '.jpg') }}');">
                    </div>

                    {{-- Overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent group-hover:from-primary/80 group-hover:via-primary/30 transition-all duration-500"></div>

                    {{-- Content on the card --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6">
                        <h3 class="text-white font-black text-2xl md:text-3xl uppercase tracking-tighter drop-shadow-2xl transition-all duration-300 group-hover:tracking-widest">
                            {{ $category->name }}
                        </h3>
                        {{-- Decorative line --}}
                        <div class="h-1 w-0 bg-white group-hover:w-20 transition-all duration-500 mt-2 rounded-full shadow-lg"></div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
