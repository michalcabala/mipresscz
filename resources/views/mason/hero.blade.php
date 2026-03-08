<div class="mason-brick mason-hero relative overflow-hidden rounded-xl bg-gray-900 text-white"
     @if($media_id ?? null)
         style="background-image: url(''); background-size: cover; background-position: center;"
     @endif>
    <div class="relative z-10 px-8 py-16 text-{{ $alignment ?? 'left' }}">
        @if($heading ?? null)
            <h1 class="text-4xl font-bold mb-4">{{ $heading }}</h1>
        @endif
        @if($subheading ?? null)
            <p class="text-xl mb-8 opacity-90">{{ $subheading }}</p>
        @endif
        @if(($button_label ?? null) && ($button_url ?? null))
            <a href="{{ $button_url }}" class="inline-block bg-white text-gray-900 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                {{ $button_label }}
            </a>
        @endif
    </div>
    @if($media_id ?? null)
        <div class="absolute inset-0 z-0">
            <x-curator-glider :media="$media_id" class="w-full h-full object-cover opacity-40" />
        </div>
    @endif
</div>

    //
</div>
