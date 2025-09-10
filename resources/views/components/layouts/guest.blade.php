<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$flux.appearance">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div class="flex min-h-screen">
        <!-- Theme Toggle for Guest Layout -->
        <div class="fixed top-4 right-4 z-50">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700">
                <flux:radio value="light" icon="sun" class="flex-1" aria-label="{{ __('Light mode') }}" title="Switch to light theme"></flux:radio>
                <flux:radio value="dark" icon="moon" class="flex-1" aria-label="{{ __('Dark mode') }}" title="Switch to dark theme"></flux:radio>
                <flux:radio value="system" icon="computer-desktop" class="flex-1" aria-label="{{ __('System theme') }}" title="Use system theme setting"></flux:radio>
            </flux:radio.group>
        </div>
        
        <!-- Main Content Area -->
        <flux:main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </flux:main>
    </div>
</body>
</html>