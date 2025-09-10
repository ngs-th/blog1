@props([
    'title' => null,
    'message',
    'variant' => 'info', // 'info', 'success', 'warning', 'error'
    'dismissible' => true,
    'icon' => null,
    'actions' => null,
    'timestamp' => null,
    'id' => null
])

@php
    $variantConfig = match($variant) {
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-950',
            'border' => 'border-green-200 dark:border-green-800',
            'icon' => $icon ?? 'check-circle',
            'iconColor' => 'text-green-600 dark:text-green-400',
            'titleColor' => 'text-green-900 dark:text-green-100',
            'messageColor' => 'text-green-800 dark:text-green-200'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-950',
            'border' => 'border-yellow-200 dark:border-yellow-800',
            'icon' => $icon ?? 'exclamation-triangle',
            'iconColor' => 'text-yellow-600 dark:text-yellow-400',
            'titleColor' => 'text-yellow-900 dark:text-yellow-100',
            'messageColor' => 'text-yellow-800 dark:text-yellow-200'
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-950',
            'border' => 'border-red-200 dark:border-red-800',
            'icon' => $icon ?? 'x-circle',
            'iconColor' => 'text-red-600 dark:text-red-400',
            'titleColor' => 'text-red-900 dark:text-red-100',
            'messageColor' => 'text-red-800 dark:text-red-200'
        ],
        default => [
            'bg' => 'bg-blue-50 dark:bg-blue-950',
            'border' => 'border-blue-200 dark:border-blue-800',
            'icon' => $icon ?? 'information-circle',
            'iconColor' => 'text-blue-600 dark:text-blue-400',
            'titleColor' => 'text-blue-900 dark:text-blue-100',
            'messageColor' => 'text-blue-800 dark:text-blue-200'
        ]
    };
    
    $notificationId = $id ?? 'notification-' . uniqid();
@endphp

<div 
    id="{{ $notificationId }}"
    class="{{ $variantConfig['bg'] }} {{ $variantConfig['border'] }} border rounded-lg p-4 shadow-sm transition-all duration-300 ease-in-out"
    role="alert"
    aria-live="polite"
>
    <div class="flex items-start gap-3">
        @if($variantConfig['icon'])
            <div class="flex-shrink-0">
                <flux:icon name="{{ $variantConfig['icon'] }}" variant="solid" class="w-5 h-5 {{ $variantConfig['iconColor'] }}" />
            </div>
        @endif
        
        <div class="flex-1 min-w-0">
            @if($title)
                <flux:heading size="sm" class="{{ $variantConfig['titleColor'] }} font-medium mb-1">
                    {{ $title }}
                </flux:heading>
            @endif
            
            <flux:text class="{{ $variantConfig['messageColor'] }} text-sm leading-relaxed">
                {{ $message }}
            </flux:text>
            
            @if($timestamp)
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ $timestamp }}
                </flux:text>
            @endif
            
            @if($actions)
                <div class="mt-3 flex gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
        
        @if($dismissible)
            <div class="flex-shrink-0">
                <flux:button 
                    variant="ghost" 
                    size="sm" 
                    icon="x-mark" 
                    class="{{ $variantConfig['iconColor'] }} hover:bg-white/50 dark:hover:bg-zinc-800/50 -m-1"
                    onclick="dismissNotification('{{ $notificationId }}')"
                    aria-label="Dismiss notification"
                />
            </div>
        @endif
    </div>
</div>

@if($dismissible)
    @push('scripts')
    <script>
        function dismissNotification(id) {
            const notification = document.getElementById(id);
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }
        
        // Auto-dismiss after 5 seconds for success notifications
        @if($variant === 'success')
            setTimeout(() => {
                dismissNotification('{{ $notificationId }}');
            }, 5000);
        @endif
    </script>
    @endpush
@endif