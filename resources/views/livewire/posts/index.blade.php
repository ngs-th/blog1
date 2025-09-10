<div>
    <!-- Page Header -->
    <div class="px-8 py-8 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="text-center">
            <flux:heading size="2xl" class="text-zinc-900 dark:text-zinc-100 font-bold mb-2">My Personal Blog</flux:heading>
            <flux:subheading class="text-zinc-600 dark:text-zinc-400">Discover insights, tutorials, and thoughts</flux:subheading>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-8 py-8 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
    
    <!-- Search and Filters -->
    <div class="mb-12" role="search" aria-label="Search and filter posts">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <!-- Search Input -->
            <div class="w-full sm:flex-1 sm:max-w-md">
                <flux:input 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search posts..."
                    icon="magnifying-glass"
                    class="w-full"
                    aria-label="Search posts by title or content"
                />
            </div>
            
            <!-- Filter Toggle and Sort -->
            <div class="flex flex-col xs:flex-row gap-2 w-full sm:w-auto">
                <flux:button 
                    variant="outline" 
                    icon="funnel" 
                    wire:click="$toggle('showFilters')"
                    class="{{ $showFilters ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-700' : '' }} w-full xs:w-auto"
                    aria-expanded="{{ $showFilters ? 'true' : 'false' }}"
                    aria-controls="filter-panel"
                    aria-label="{{ $showFilters ? 'Hide filters' : 'Show filters' }}"
                >
                    Filters
                </flux:button>
                
                <flux:select 
                    wire:model.live="sortBy" 
                    class="w-full xs:min-w-32"
                    aria-label="Sort posts by"
                >
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                    <option value="title">Title A-Z</option>
                </flux:select>
            </div>
        </div>
        
        <!-- Expandable Filters -->
        @if($showFilters)
            <div 
                id="filter-panel"
                class="mt-6 p-4 sm:p-6 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 mb-4"
                role="region"
                aria-label="Filter options"
            >
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <div class="flex-1">
                        <flux:field>
                            <flux:label for="author-filter" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Author</flux:label>
                            <flux:select 
                                id="author-filter"
                                wire:model.live="authorFilter" 
                                placeholder="All authors"
                                aria-label="Filter posts by author"
                                class="w-full mt-1"
                            >
                                <option value="">All authors</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author }}">{{ $author }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>
                    
                    <div class="flex items-end">
                        <flux:button 
                            variant="outline" 
                            wire:click="clearFilters"
                            icon="x-mark"
                            aria-label="Clear all filters"
                            class="w-full sm:w-auto"
                        >
                            Clear
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Active Filters Display -->
        @if($search || $authorFilter || $sortBy !== 'latest')
            <div class="mt-4 flex flex-wrap gap-2" role="status" aria-label="Active filters">
                @if($search)
                    <flux:badge variant="outline">
                        Search: "{{ $search }}"
                        <flux:button 
                            variant="ghost" 
                            size="sm" 
                            icon="x-mark" 
                            wire:click="$set('search', '')"
                            class="ml-1 -mr-1"
                        />
                    </flux:badge>
                @endif
                
                @if($authorFilter)
                    <flux:badge variant="outline">
                        Author: {{ $authorFilter }}
                        <flux:button 
                            variant="ghost" 
                            size="sm" 
                            icon="x-mark" 
                            wire:click="$set('authorFilter', '')"
                            class="ml-1 -mr-1"
                        />
                    </flux:badge>
                @endif
                
                @if($sortBy !== 'latest')
                    <flux:badge variant="outline">
                        Sort: {{ ucfirst($sortBy) }}
                        <flux:button 
                            variant="ghost" 
                            size="sm" 
                            icon="x-mark" 
                            wire:click="$set('sortBy', 'latest')"
                            class="ml-1 -mr-1"
                        />
                    </flux:badge>
                @endif
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div 
        wire:loading.delay 
        class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        role="status"
        aria-label="Loading posts"
        aria-live="polite"
    >
        <x-post-skeleton :count="6" />
    </div>
    
    <!-- Posts Grid -->
    <div 
        wire:loading.remove.delay 
        class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        role="main"
        aria-label="Blog posts"
        aria-live="polite"
        aria-busy="false"
    >
        @forelse ($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="col-span-full">
                @if($search || $authorFilter)
                    <!-- No results found -->
                    <flux:card class="text-center py-12">
                        <flux:icon name="magnifying-glass" class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                        <flux:heading size="lg" class="mb-2">No posts found</flux:heading>
                        <flux:subheading class="mb-4">Try adjusting your search or filters</flux:subheading>
                        <flux:button variant="outline" wire:click="clearFilters">
                            Clear all filters
                        </flux:button>
                    </flux:card>
                @else
                    <!-- No posts at all -->
                    <flux:card class="text-center py-12">
                        <flux:icon name="document-text" class="mx-auto h-12 w-12 text-zinc-400 mb-4" />
                        <flux:heading size="lg" class="mb-2">No posts published yet</flux:heading>
                        <flux:subheading>Check back later for new articles!</flux:subheading>
                    </flux:card>
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-16">
        {{ $posts->links() }}
    </div>

<script>
    // Handle post action events
    document.addEventListener('livewire:init', () => {
        Livewire.on('post-action', (event) => {
            const data = event[0];
            // Show toast notification (you can replace with your preferred notification system)
            console.log(`${data.type}: ${data.message}`);
            
            // Optional: Show a simple alert for now
            if (data.message) {
                // Create a simple toast notification
                showToast(data.message, data.type.includes('like') ? 'success' : 'info');
            }
        });
        
        Livewire.on('copy-to-clipboard', (event) => {
            const data = event[0];
            
            // Copy URL to clipboard
            navigator.clipboard.writeText(data.url).then(() => {
                showToast(data.message, 'success');
            }).catch(() => {
                showToast('Failed to copy URL', 'error');
            });
        });
    });
    
    // Simple toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
</script>
    </div>
</div>
