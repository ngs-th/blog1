@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null, // 'up', 'down', 'neutral'
    'trendValue' => null,
    'description' => null,
    'variant' => 'default', // 'default', 'primary', 'success', 'warning', 'danger'
    'size' => 'default' // 'sm', 'default', 'lg'
])

@php
    $cardClasses = match($variant) {
        'primary' => 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950',
        'success' => 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950',
        'warning' => 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950',
        'danger' => 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950',
        default => 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900'
    };
    
    $iconClasses = match($variant) {
        'primary' => 'text-blue-600 dark:text-blue-400',
        'success' => 'text-green-600 dark:text-green-400',
        'warning' => 'text-yellow-600 dark:text-yellow-400',
        'danger' => 'text-red-600 dark:text-red-400',
        default => 'text-zinc-600 dark:text-zinc-400'
    };
    
    // Map variant to valid Flux icon variant
    $iconVariant = match($variant) {
        'primary', 'success', 'warning', 'danger' => 'solid',
        default => 'outline'
    };
    
    $trendClasses = match($trend) {
        'up' => 'text-green-600 dark:text-green-400',
        'down' => 'text-red-600 dark:text-red-400',
        'neutral' => 'text-zinc-600 dark:text-zinc-400',
        default => 'text-zinc-600 dark:text-zinc-400'
    };
    
    $trendIcon = match($trend) {
        'up' => 'arrow-trending-up',
        'down' => 'arrow-trending-down',
        'neutral' => 'minus',
        default => null
    };
    
    $padding = match($size) {
        'sm' => 'p-4',
        'lg' => 'p-8',
        default => 'p-6'
    };
    
    $valueSize = match($size) {
        'sm' => 'text-2xl',
        'lg' => 'text-4xl',
        default => 'text-3xl'
    };
@endphp

<flux:card class="{{ $cardClasses }} {{ $padding }} hover:shadow-lg transition-all duration-200">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                @if($icon)
                    <div class="p-2 rounded-lg bg-white/50 dark:bg-zinc-800/50">
                        <flux:icon name="{{ $icon }}" variant="{{ $iconVariant }}" class="w-5 h-5 {{ $iconClasses }}" />
                    </div>
                @endif
                <flux:text class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                    {{ $title }}
                </flux:text>
            </div>
            
            <div class="mb-2">
                <flux:heading size="xl" class="{{ $valueSize }} font-bold text-zinc-900 dark:text-zinc-100">
                    {{ $value }}
                </flux:heading>
            </div>
            
            @if($description)
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-500 mb-2">
                    {{ $description }}
                </flux:text>
            @endif
            
            @if($trend && $trendValue)
                <div class="flex items-center gap-1">
                    @if($trendIcon)
                        <flux:icon name="{{ $trendIcon }}" variant="micro" class="{{ $trendClasses }}" />
                    @endif
                    <flux:text class="text-sm font-medium {{ $trendClasses }}">
                        {{ $trendValue }}
                    </flux:text>
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-500">
                        vs last period
                    </flux:text>
                </div>
            @endif
        </div>
    </div>
</flux:card>