@props([
    'sortable' => false,
    'direction' => null,
    'sortBy' => null,
    'width' => null,
])

@php
    $thClasses = Arr::toCssClasses([
        'px-4 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider',
        'cursor-pointer' => $sortable,
        'text-right' => $attributes->get('align') === 'right',
        'text-center' => $attributes->get('align') === 'center',
    ]);
@endphp

<th
    {{ $attributes->merge(['class' => $thClasses]) }}
    @if($width) style="width: {{ $width }}" @endif
>
    @if($sortable)
        <div class="flex hover:text-black items-center gap-x-1" wire:click="sortBy('{{ $sortBy }}')">
            <span>{{ $slot }}</span>

            @if($direction === 'asc')
                <x-flux::icon name="chevron-up" class="h-4 w-4" />
            @elseif($direction === 'desc')
                <x-flux::icon name="chevron-down" class="h-4 w-4" />
            @else
                <x-flux::icon name="arrows-up-down" class="h-4 w-4 opacity-30" />
            @endif
        </div>
    @else
        {{ $slot }}
    @endif
</th>
