@props([
    'striped' => true,
    'hover' => true,
    'bordered' => false,
    'responsive' => true,
    'id' => null,
])

@php
    $tableClasses = Arr::toCssClasses([
        'w-full text-left border-collapse',
        'border border-gray-200' => $bordered,
    ]);

    $tbodyClasses = Arr::toCssClasses([
        'divide-y divide-gray-200' => $striped,
    ]);
@endphp

<div @class(['overflow-x-auto' => $responsive])>
    <table
        {{ $attributes->merge(['class' => $tableClasses]) }}
        @if($id) id="{{ $id }}" @endif
    >
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                {{ $head }}
            </tr>
        </thead>
        <tbody {{ $attributes->merge(['class' => $tbodyClasses]) }}>
            {{ $body }}
        </tbody>
    </table>
</div>
