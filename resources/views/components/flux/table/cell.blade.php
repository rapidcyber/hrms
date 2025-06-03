@props(['colspan' => null, 'rowspan' => null])

@php
    $tdClasses = Arr::toCssClasses([
        'px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100',
        'text-right' => $attributes->get('align') === 'right',
        'text-center' => $attributes->get('align') === 'center',
    ]);
@endphp

<td
    {{ $attributes->merge(['class' => $tdClasses]) }}
    @if($colspan) colspan="{{ $colspan }}" @endif
    @if($rowspan) rowspan="{{ $rowspan }}" @endif
>
    {{ $slot }}
</td>
