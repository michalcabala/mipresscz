@php
    $ratios = [
        '1:1' => ['w-1/2', 'w-1/2'],
        '1:2' => ['w-1/3', 'w-2/3'],
        '2:1' => ['w-2/3', 'w-1/3'],
        '1:3' => ['w-1/4', 'w-3/4'],
        '3:1' => ['w-3/4', 'w-1/4'],
    ];
    $widths = $ratios[$ratio ?? '1:1'] ?? ['w-1/2', 'w-1/2'];
@endphp
<div class="mason-brick mason-columns flex flex-wrap gap-8">
    <div class="{{ $widths[0] }} min-w-0 prose max-w-none">
        {!! $left ?? '' !!}
    </div>
    <div class="{{ $widths[1] }} min-w-0 prose max-w-none">
        {!! $right ?? '' !!}
    </div>
</div>

    //
</div>
