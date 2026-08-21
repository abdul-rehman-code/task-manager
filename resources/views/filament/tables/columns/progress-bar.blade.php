<div class="fi-ta-text grid gap-y-1 px-3 py-4">
    @php
        $state = $getState();
        $color = match(true) {
            $state >= 75 => 'bg-success-500',
            $state >= 25 => 'bg-warning-500',
            default => 'bg-danger-500',
        };
    @endphp
    
    <div class="flex items-center gap-2 w-full min-w-[120px]">
        <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full {{ $color }} rounded-full" style="width: {{ $state }}%"></div>
        </div>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 w-10 text-right">
            {{ $state }}%
        </span>
    </div>
</div>
