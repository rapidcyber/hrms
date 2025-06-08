<x-layouts.app :title="__('Dashboard')">
    <div class="flex min-h-[calc(100vh-70px)] w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                @livewire('dashboard.employees')
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="relative size-full p-5" @click="location.href='/employees'">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Payrolls</h2>
                    @livewire('dashboard.payrolls')
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="relative size-full p-5" @click="location.href='/employees'">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Sync Biometrics</h2>
                    <div class="flex mt-4 items-center bg-white rounded-lg p-4 gap-4 shadow-md dark:bg-gray-800 dark:shadow-gray-700">
                        <div class="flex flex-col flex-col text-gray-600 dark:text-gray-300">
                            <div class="flex items-center gap-2">
                                <em>Last sync:</em>
                                <strong class="text-lg text-purple-400">{{now()->format('F j, Y h:i A')}}</strong>
                            </div>
                            <div class="flex gap-2 justify-end">
                                <x-flux::button icon="refresh-ccw" variant="primary">Sync Now</x-flux::button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            @livewire('dashboard.attendances')
        </div>
    </div>
</x-layouts.app>
