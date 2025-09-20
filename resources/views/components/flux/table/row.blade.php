@props(['even' => false])

@php
    $trClasses = Arr::toCssClasses([
        'hover:bg-gray-50 dark:hover:bg-gray-700' => $hover,
        'bg-gray-50/50 dark:bg-gray-800/50' => $even,
    ]);
@endphp

<tr {{ $attributes->merge(['class' => $trClasses]) }}>
    {{ $slot }}
</tr>
