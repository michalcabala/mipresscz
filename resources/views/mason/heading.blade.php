@if($text)
    <div class="mason-brick mason-heading text-{{ $alignment ?? 'left' }}">
        <{{ $level ?? 'h2' }} class="font-bold leading-tight">
            {{ $text }}
        </{{ $level ?? 'h2' }}>
    </div>
@endif

    //
</div>
