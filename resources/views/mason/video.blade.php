@if($url)
    @php
        $embedUrl = null;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $url, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            $embedUrl = 'https://player.vimeo.com/video/' . $m[1];
        }
    @endphp
    <div class="mason-brick mason-video">
        @if($embedUrl)
            <div class="relative aspect-video overflow-hidden rounded">
                <iframe src="{{ $embedUrl }}" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>
            </div>
        @else
            <video src="{{ $url }}" controls class="w-full rounded"></video>
        @endif
        @if($caption ?? null)
            <p class="mt-2 text-sm text-center text-gray-600">{{ $caption }}</p>
        @endif
    </div>
@endif

    //
</div>
