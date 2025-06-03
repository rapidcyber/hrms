@props(['id' => null, 'maxWidth' => '2xl', 'title' => null])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        default => 'max-w-2xl',
    };
@endphp

<div x-data="{ show: @entangle($attributes->wire('show')) }" x-cloak>
    <!-- Overlay -->
    <div
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <!-- Modal -->
        <div
            x-show="show"
            x-transition
            @click.outside="show = false"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full {{ $maxWidthClass }} p-6 relative"
        >
            <!-- Close button -->
            {{-- <button
                @click="show = false"
                class="absolute top-2 right-2 rounded px-3 hover:bg-gray-100 text-2xl text-gray-500 hover:text-gray-700 dark:text-gray-300"
            >
                &times;
            </button> --}}
            <div class="absolute top-3 right-3">
                <x-flux::button @click="show = false" size="sm" icon="x-mark" variant="ghost" inset />
            </div>

            <!-- Title -->
            @if($title)
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">{{ $title }}</h2>
            @endif

            <!-- Content -->
            <div class="mb-4">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @isset($footer)
                <div class="mt-4 border-t pt-4 flex justify-end">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
