<div class="flex mt-4 items-center bg-white rounded-lg p-4 gap-4 shadow-md dark:bg-gray-800 dark:shadow-gray-700">
    <div class="w-fit p-2 rounded-full bg-gray-100">
        <flux:icon.hand-coins class="size-6 text-gray-500 dark:text-gray-400" />
    </div>
    <div class="flex flex-col flex-col gap-2 text-gray-600 dark:text-gray-300">
        <div class="flex gap-2">
            <em>This {{now()->format('F')}}</em>
        </div>
        <div class="flex gap-2">
            <strong>Processed</strong>
            <strong class="text-lg text-purple-400">{{$payrolls->count() ?? 0}}</strong>
        </div>
    </div>
</div>
