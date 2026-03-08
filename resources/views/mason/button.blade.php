@if(($label ?? null) && ($url ?? null))
    @php
        $variantClasses = match($variant ?? 'primary') {
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'outline' => 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50',
            default => 'bg-primary-600 hover:bg-primary-700 text-white',
        };
        $alignClass = match($alignment ?? 'left') {
            'center' => 'text-center',
            'right' => 'text-right',
            default => 'text-left',
        };
    @endphp
    <div class="mason-brick mason-button {{ $alignClass }}">
        <a href="{{ $url }}"
           class="inline-block font-semibold px-6 py-3 rounded-lg transition {{ $variantClasses }}"
           @if($open_in_new_tab ?? false) target="_blank" rel="noopener noreferrer" @endif>
            {{ $label }}
        </a>
    </div>
@endif

    //
</div>
