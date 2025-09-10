<div class="container max-w-4xl mx-auto px-4 overflow-hidden">
    <nav class="mb-6" aria-label="Breadcrumb">
        <flux:button 
            variant="ghost" 
            icon="arrow-left" 
            onclick="window.location.href='{{ route('home') }}'"
            aria-label="Go back to posts"
        >
            Posts
        </flux:button>
    </nav>

    <flux:card class="overflow-hidden">
        <div class="p-8">
            <article role="article" aria-labelledby="article-title">
                <header class="mb-8 pb-6 border-b border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="3xl" class="mb-4" id="article-title">
                        {{ $post->title }}
                    </flux:heading>
                    
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4">
                        <flux:badge variant="outline" class="flex-shrink-0">
                            <flux:icon name="user" class="w-4 h-4 mr-2" />
                            {{ $post->user->name }}
                        </flux:badge>
                        <flux:badge variant="subtle" class="flex-shrink-0">
                            <flux:icon name="calendar" class="w-4 h-4 mr-2" />
                            {{ $post->published_at->format('F j, Y') }}
                        </flux:badge>
                        <flux:badge variant="subtle" class="flex-shrink-0">
                            <flux:icon name="clock" class="w-4 h-4 mr-2" />
                            {{ $post->published_at->diffForHumans() }}
                        </flux:badge>
                    </div>
                    
                    <div class="flex flex-wrap gap-2" role="toolbar" aria-label="Post actions">
                         @php
                             $isLiked = $this->isPostLiked($post->id) ?? false;
                             $isBookmarked = $this->isPostBookmarked($post->id) ?? false;
                         @endphp
                         
                         <flux:button 
                             variant="outline" 
                             size="sm" 
                             icon="heart"
                             wire:click="likePost({{ $post->id }})"
                             class="{{ $isLiked ? 'text-red-500 border-red-500 hover:bg-red-50' : 'hover:text-red-500 hover:border-red-500' }} transition-colors"
                             aria-label="{{ $isLiked ? 'Unlike' : 'Like' }} this post"
                         >
                             {{ $isLiked ? 'Unlike' : 'Like' }}
                         </flux:button>
                         <flux:button 
                             variant="outline" 
                             size="sm" 
                             icon="bookmark"
                             wire:click="bookmarkPost({{ $post->id }})"
                             class="{{ $isBookmarked ? 'text-blue-500 border-blue-500 hover:bg-blue-50' : 'hover:text-blue-500 hover:border-blue-500' }} transition-colors"
                             aria-label="{{ $isBookmarked ? 'Remove bookmark' : 'Bookmark' }} this post"
                         >
                             {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                         </flux:button>
                         <flux:button 
                             variant="outline" 
                             size="sm" 
                             icon="share"
                             wire:click="sharePost({{ $post->id }})"
                             class="hover:text-green-500 hover:border-green-500 transition-colors"
                             aria-label="Share this post"
                         >
                             Share
                         </flux:button>
                     </div>
                </header>

                <div 
                    class="prose prose-lg prose-zinc dark:prose-invert max-w-none"
                    role="main"
                    aria-label="Article content"
                >
                    <div class="text-zinc-800 dark:text-zinc-200 leading-relaxed">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </div>
            </article>

            <footer class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <flux:text size="sm" class="text-zinc-500">
                            <time datetime="{{ $post->published_at->toISOString() }}">
                                Published {{ $post->published_at->diffForHumans() }}
                            </time>
                        </flux:text>
                        <div class="flex flex-wrap gap-2" role="toolbar" aria-label="Post actions">
                             <flux:button 
                                 variant="ghost" 
                                 size="sm" 
                                 icon="heart" 
                                 wire:click="likePost({{ $post->id }})"
                                 class="{{ $isLiked ? 'text-red-500 hover:text-red-600' : 'hover:text-red-500' }} transition-colors"
                                 aria-label="{{ $isLiked ? 'Unlike' : 'Like' }} this post"
                                 title="{{ $isLiked ? 'Unlike' : 'Like' }} this post"
                             />
                             <flux:button 
                                 variant="ghost" 
                                 size="sm" 
                                 icon="bookmark" 
                                 wire:click="bookmarkPost({{ $post->id }})"
                                 class="{{ $isBookmarked ? 'text-blue-500 hover:text-blue-600' : 'hover:text-blue-500' }} transition-colors"
                                 aria-label="{{ $isBookmarked ? 'Remove bookmark' : 'Bookmark' }} this post"
                                 title="{{ $isBookmarked ? 'Remove bookmark' : 'Bookmark' }} this post"
                             />
                             <flux:button 
                                 variant="ghost" 
                                 size="sm" 
                                 icon="share" 
                                 wire:click="sharePost({{ $post->id }})"
                                 class="hover:text-green-500 transition-colors"
                                 aria-label="Share this post"
                                 title="Share this post"
                             />
                         </div>
                    </div>
                    
                </div>
            </footer>
        </div>
    </flux:card>
    
    <div class="mt-6">
        <flux:button 
            variant="ghost" 
            icon="arrow-left" 
            onclick="window.location.href='{{ route('home') }}'"
            aria-label="Go back to all posts"
        >
            Back to all posts
        </flux:button>
    </div>
</div>

<script>
    // Handle post action events
    document.addEventListener('livewire:init', () => {
        Livewire.on('post-action', (event) => {
            const data = event[0];
            // Show toast notification
            console.log(`${data.type}: ${data.message}`);
            
            if (data.message) {
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
