@if($quote ?? null)
    <div class="mason-brick mason-testimonial bg-gray-50 rounded-xl p-8">
        <blockquote class="text-lg text-gray-800 italic mb-6">"{!! $quote !!}"</blockquote>
        <div class="flex items-center gap-4">
            @if($media_id ?? null)
                <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                    <x-curator-glider :media="$media_id" class="w-full h-full object-cover" />
                </div>
            @endif
            <div>
                @if($author ?? null)
                    <p class="font-semibold text-gray-900">{{ $author }}</p>
                @endif
                @if($company ?? null)
                    <p class="text-sm text-gray-600">{{ $company }}</p>
                @endif
                @if($rating ?? null)
                    <div class="flex gap-1 mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-{{ $i <= $rating ? 'yellow' : 'gray' }}-400">★</span>
                        @endfor
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

    //
</div>
