@if(!empty($media_ids))
    <div class="mason-brick mason-gallery grid gap-4" style="grid-template-columns: repeat({{ $columns ?? 3 }}, minmax(0, 1fr))">
        @foreach($media_ids as $mediaId)
            <div class="overflow-hidden rounded">
                <x-curator-glider :media="$mediaId" class="w-full h-auto object-cover" />
            </div>
        @endforeach
    </div>
@endif

    //
</div>
