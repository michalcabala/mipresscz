@if($media_id)
    <div class="mason-brick mason-image">
        <figure>
            <x-curator-glider :media="$media_id" class="w-full h-auto rounded" />
            @if($caption)
                <figcaption class="mt-2 text-sm text-center text-gray-600">{{ $caption }}</figcaption>
            @endif
        </figure>
    </div>
@endif

    //
</div>
