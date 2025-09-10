@props([
    'post',
    'showActions' => true,
    'variant' => 'default' // 'default', 'compact', 'featured'
])

@php
    $isLiked = $this->isPostLiked($post->id) ?? false;
    $isBookmarked = $this->isPostBookmarked($post->id) ?? false;
@endphp

<flux:card 
    class="group hover:shadow-lg transition-all duration-200 cursor-pointer {{ $variant === 'featured' ? 'md:col-span-2 lg:col-span-3' : '' }}"
    onclick="window.location.href='{{ route('posts.show', $post) }}'"
    role="article"
    aria-labelledby="post-title-{{ $post->id }}"
    aria-describedby="post-excerpt-{{ $post->id }}"
    tabindex="0"
    onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }"
>
    <div class="p-6 {{ $variant === 'featured' ? 'md:p-8' : '' }}">
        @if($variant === 'featured')
            <div class="mb-4">
                <flux:badge variant="primary" size="sm">
                    <flux:icon name="star" class="w-3 h-3 mr-1" />
                    Featured
                </flux:badge>
            </div>
        @endif
        
        <flux:heading 
            size="{{ $variant === 'featured' ? 'xl' : ($variant === 'compact' ? 'md' : 'lg') }}" 
            class="mb-3 group-hover:text-blue-600 transition-colors"
            id="post-title-{{ $post->id }}"
        >
            {{ $post->title }}
        </flux:heading>
        
        <div class="flex items-center gap-2 mb-4 flex-wrap">
            <flux:badge variant="outline" size="sm">
                <flux:icon name="user" class="w-3 h-3 mr-1" />
                {{ $post->user->name }}
            </flux:badge>
            <flux:badge variant="subtle" size="sm">
                <flux:icon name="calendar" class="w-3 h-3 mr-1" />
                {{ $post->published_at->format('M d, Y') }}
            </flux:badge>
            @if($variant === 'featured')
                <flux:badge variant="subtle" size="sm">
                    <flux:icon name="clock" class="w-3 h-3 mr-1" />
                    {{ $post->published_at->diffForHumans() }}
                </flux:badge>
            @endif
        </div>
        
        <flux:text 
            class="text-zinc-600 dark:text-zinc-400 mb-4 {{ $variant === 'compact' ? 'line-clamp-2' : 'line-clamp-3' }}"
            id="post-excerpt-{{ $post->id }}"
        >
            {{ Str::limit(strip_tags($post->content), $variant === 'featured' ? 300 : ($variant === 'compact' ? 100 : 150)) }}
        </flux:text>
        
        <div class="flex justify-between items-center">
            <flux:button 
                variant="ghost" 
                size="sm" 
                icon="arrow-right"
                onclick="window.location.href='{{ route('posts.show', $post) }}'; event.stopPropagation();"
                aria-label="Read full article: {{ $post->title }}"
            >
                Read more
            </flux:button>
            
            @if($showActions)
                <div class="flex gap-1">
                    <flux:button 
                        variant="ghost" 
                        size="sm" 
                        icon="heart" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity {{ $isLiked ? 'text-red-500 hover:text-red-600' : 'hover:text-red-500' }}"
                        wire:click.stop="likePost({{ $post->id }})"
                        aria-label="{{ $isLiked ? 'Unlike' : 'Like' }} this post"
                        title="{{ $isLiked ? 'Unlike' : 'Like' }} this post"
                    />
                    <flux:button 
                        variant="ghost" 
                        size="sm" 
                        icon="bookmark" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity {{ $isBookmarked ? 'text-blue-500 hover:text-blue-600' : 'hover:text-blue-500' }}"
                        wire:click.stop="bookmarkPost({{ $post->id }})"
                        aria-label="{{ $isBookmarked ? 'Remove bookmark' : 'Bookmark' }} this post"
                        title="{{ $isBookmarked ? 'Remove bookmark' : 'Bookmark' }} this post"
                    />
                    <flux:button 
                        variant="ghost" 
                        size="sm" 
                        icon="share" 
                        class="opacity-0 group-hover:opacity-100 transition-opacity hover:text-green-500"
                        wire:click.stop="sharePost({{ $post->id }})"
                        aria-label="Share this post"
                        title="Share this post"
                    />
                </div>
            @endif
        </div>
    </div>
</flux:card>