<div class="max-w-4xl mx-auto p-6">
    {{-- Page Header - Simplified without flux:header to avoid navbar conflicts --}}
    <div class="mb-8">
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item :href="route('admin.posts.index')" icon="arrow-left">Posts</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Edit Post</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" class="mb-2">Edit Post</flux:heading>
                <flux:subheading>Update your blog article content and settings</flux:subheading>
            </div>
            
            <flux:button 
                :href="route('posts.show', $post)"
                variant="outline"
                icon="eye"
                target="_blank"
                class="hidden sm:flex"
            >
                View Post
            </flux:button>
        </div>
        
        {{-- Mobile View Post Button --}}
        <div class="mt-4 sm:hidden">
            <flux:button 
                :href="route('posts.show', $post)"
                variant="outline"
                icon="eye"
                target="_blank"
                class="w-full"
            >
                View Post
            </flux:button>
        </div>
    </div>

    <!-- Current Status Banner -->
    @if($post->published_at)
        <flux:callout variant="success" class="mb-6">
            <flux:callout.text>
                This post is currently <strong>published</strong> and visible to readers.
                Published on {{ $post->published_at->format('M j, Y \\a\\t g:i A') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:callout variant="warning" class="mb-6">
            <flux:callout.text>
                This post is currently saved as a <strong>draft</strong> and not visible to readers.
            </flux:callout.text>
        </flux:callout>
    @endif

    <x-admin.post-form 
         :post="$post"
         submit-text="Update Post"
         submit-action="update"
         :show-status="true"
     />
</div>
