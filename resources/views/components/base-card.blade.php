@props([
    'variant' => 'default', // 'default', 'outlined', 'elevated', 'flat'
    'padding' => 'default', // 'none', 'sm', 'default', 'lg', 'xl'
    'rounded' => 'default', // 'none', 'sm', 'default', 'lg', 'xl', 'full'
    'shadow' => 'default', // 'none', 'sm', 'default', 'lg', 'xl'
    'hover' => false,
    'clickable' => false,
    'loading' => false,
    'disabled' => false
])

@php
    $baseClasses = 'transition-all duration-200';
    
    $variantClasses = match($variant) {
        'outlined' => 'bg-transparent border-2 border-zinc-200 dark:border-zinc-700',
        'elevated' => 'bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 shadow-lg',
        'flat' => 'bg-zinc-50 dark:bg-zinc-800 border-0',
        default => 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800'
    };
    
    $paddingClasses = match($padding) {
        'none' => '',
        'sm' => 'p-3',
        'lg' => 'p-8',
        'xl' => 'p-12',
        default => 'p-6'
    };
    
    $roundedClasses = match($rounded) {
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
        default => 'rounded-md'
    };
    
    $shadowClasses = match($shadow) {
        'none' => 'shadow-none',
        'sm' => 'shadow-sm',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
        default => 'shadow'
    };
    
    $interactionClasses = '';
    if ($clickable && !$disabled) {
        $interactionClasses .= ' cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900';
    }
    
    if ($hover && !$disabled) {
        $interactionClasses .= ' hover:shadow-lg hover:-translate-y-0.5';
    }
    
    if ($disabled) {
        $interactionClasses .= ' opacity-50 cursor-not-allowed';
    }
    
    $allClasses = collect([
        $baseClasses,
        $variantClasses,
        $paddingClasses,
        $roundedClasses,
        $shadowClasses,
        $interactionClasses
    ])->filter()->implode(' ');
@endphp

<div 
    {{ $attributes->merge(['class' => $allClasses]) }}
    @if($clickable && !$disabled)
        role="button"
        tabindex="0"
        onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }"
    @endif
>
    @if($loading)
        <div class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
    @else
        {{ $slot }}
    @endif
</div>