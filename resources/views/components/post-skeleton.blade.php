@props([
    'count' => 6
])

@for($i = 0; $i < $count; $i++)
    <div class="animate-pulse">
        <flux:card class="overflow-hidden">
            <div class="p-6">
                <!-- Title skeleton -->
                <div class="h-6 bg-zinc-200 dark:bg-zinc-700 rounded mb-3"></div>
                
                <!-- Badges skeleton -->
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-5 w-20 bg-zinc-200 dark:bg-zinc-700 rounded-full"></div>
                    <div class="h-5 w-16 bg-zinc-200 dark:bg-zinc-700 rounded-full"></div>
                </div>
                
                <!-- Content skeleton -->
                <div class="space-y-2 mb-4">
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded"></div>
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-5/6"></div>
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-4/6"></div>
                </div>
                
                <!-- Actions skeleton -->
                <div class="flex justify-between items-center">
                    <div class="h-8 w-24 bg-zinc-200 dark:bg-zinc-700 rounded"></div>
                    <div class="flex gap-1">
                        <div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-700 rounded"></div>
                        <div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-700 rounded"></div>
                        <div class="h-8 w-8 bg-zinc-200 dark:bg-zinc-700 rounded"></div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
@endfor