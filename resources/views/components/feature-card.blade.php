@props([
    'title',
    'description',
    'icon' => null,
    'iconColor' => 'blue', // 'blue', 'green', 'purple', 'red', 'yellow', 'gray'
    'href' => null,
    'target' => '_self',
    'badge' => null,
    'badgeVariant' => 'primary',
    'layout' => 'vertical', // 'vertical', 'horizontal'
    'size' => 'default' // 'sm', 'default', 'lg'
])

@php
    $iconColorClasses = match($iconColor) {
        'green' => 'text-green-600 bg-green-100 dark:text-green-400 dark:bg-green-900/30',
        'purple' => 'text-purple-600 bg-purple-100 dark:text-purple-400 dark:bg-purple-900/30',
        'red' => 'text-red-600 bg-red-100 dark:text-red-400 dark:bg-red-900/30',
        'yellow' => 'text-yellow-600 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30',
        'gray' => 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-900/30',
        default => 'text-blue-600 bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30'
    };
    
    $iconSize = match($size) {
        'sm' => 'w-5 h-5',
        'lg' => 'w-8 h-8',
        default => 'w-6 h-6'
    };
    
    $iconContainerSize = match($size) {
        'sm' => 'w-10 h-10',
        'lg' => 'w-16 h-16',
        default => 'w-12 h-12'
    };
    
    $titleSize = match($size) {
        'sm' => 'text-base',
        'lg' => 'text-xl',
        default => 'text-lg'
    };
    
    $descriptionSize = match($size) {
        'sm' => 'text-sm',
        'lg' => 'text-base',
        default => 'text-sm'
    };
    
    $padding = match($size) {
        'sm' => 'p-4',
        'lg' => 'p-8',
        default => 'p-6'
    };
    
    $isClickable = !empty($href);
    $layoutClasses = $layout === 'horizontal' ? 'flex items-start gap-4' : 'text-center';
@endphp

@if($isClickable)
    <a href="{{ $href }}" target="{{ $target }}" class="block group">
@endif

<x-base-card 
    :hover="$isClickable"
    :clickable="$isClickable"
    padding="none"
    class="{{ $padding }} {{ $isClickable ? 'group-hover:border-blue-300 dark:group-hover:border-blue-600' : '' }}"
>
    <div class="{{ $layoutClasses }}">
        @if($icon)
            <div class="{{ $layout === 'horizontal' ? 'flex-shrink-0' : 'mx-auto mb-4' }}">
                <div class="{{ $iconContainerSize }} rounded-lg {{ $iconColorClasses }} flex items-center justify-center {{ $isClickable ? 'group-hover:scale-110 transition-transform duration-200' : '' }}">
                    <flux:icon name="{{ $icon }}" class="{{ $iconSize }}" />
                </div>
            </div>
        @endif
        
        <div class="{{ $layout === 'horizontal' ? 'flex-1 min-w-0' : '' }}">
            <div class="flex items-center {{ $layout === 'horizontal' ? 'justify-start' : 'justify-center' }} gap-2 mb-2">
                <flux:heading size="md" class="{{ $titleSize }} font-semibold text-zinc-900 dark:text-zinc-100 {{ $isClickable ? 'group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors' : '' }}">
                    {{ $title }}
                </flux:heading>
                
                @if($badge)
                    <flux:badge variant="{{ $badgeVariant }}" size="sm">
                        {{ $badge }}
                    </flux:badge>
                @endif
            </div>
            
            <flux:text class="{{ $descriptionSize }} text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {{ $description }}
            </flux:text>
            
            @if($isClickable)
                <div class="mt-3 flex items-center {{ $layout === 'horizontal' ? 'justify-start' : 'justify-center' }} text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors">
                    <flux:text class="text-sm font-medium mr-1">
                        Learn more
                    </flux:text>
                    <flux:icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" />
                </div>
            @endif
        </div>
    </div>
</x-base-card>

@if($isClickable)
    </a>
@endif