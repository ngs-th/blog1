<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    @if (session()->has('message'))
        <flux:callout variant="success" class="mb-8">
            {{ session('message') }}
        </flux:callout>
    @endif

    <!-- Page Header -->
    <div class="px-8 py-8 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100 font-bold">Your Posts</flux:heading>
                <flux:subheading class="text-zinc-600 dark:text-zinc-400 mt-2">Manage and organize your blog content</flux:subheading>
            </div>
            <flux:button :href="route('admin.posts.create')" variant="primary" icon="plus">
                Add New Post
            </flux:button>
        </div>

        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <flux:select size="sm" wire:model.live="dateFilter">
                        <option value="7">Last 7 days</option>
                        <option value="14">Last 14 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="60">Last 60 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="all">All time</option>
                    </flux:select>

                    <flux:subheading class="max-md:hidden whitespace-nowrap">posts</flux:subheading>
                </div>

                <flux:separator vertical class="max-lg:hidden mx-2 my-2" />

                <div class="max-lg:hidden flex justify-start items-center gap-2">
                    <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg" wire:click="toggleStatusFilter">Status</flux:badge>
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg" class="max-md:hidden" wire:click="toggleAuthorFilter">Author</flux:badge>
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg" wire:click="toggleMoreFilters">More filters...</flux:badge>
                </div>
            </div>


        </div>
    </div>

    <div class="space-y-24 px-8 py-8">
        <!-- Statistics Cards -->
        <div class="flex gap-6">
            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
                <flux:subheading>Total Posts</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $totalPosts }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-blue-600 dark:text-blue-400">
                    <flux:icon icon="document-text" variant="micro" /> All content
                </div>
            </div>

            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700 max-md:hidden">
                <flux:subheading>Published</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $publishedPosts }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-green-600 dark:text-green-400">
                    <flux:icon icon="arrow-trending-up" variant="micro" /> Live posts
                </div>
            </div>

            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700 max-lg:hidden">
                <flux:subheading>Drafts</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $draftPosts }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-yellow-600 dark:text-yellow-400">
                    <flux:icon icon="clock" variant="micro" /> In progress
                </div>
            </div>
        </div>

        <!-- Separator Line -->
        <div class="border-t border-zinc-200 dark:border-zinc-700"></div>

        @if(count($selectedPosts ?? []) > 0)
            <div class="mb-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:subheading>{{ count($selectedPosts) }} post(s) selected</flux:subheading>
                    </div>
                    <div class="flex items-center gap-2">
                        <flux:button size="sm" variant="ghost" wire:click="bulkPublish" icon="eye">
                            Publish Selected
                        </flux:button>
                        <flux:button size="sm" variant="ghost" wire:click="bulkUnpublish" icon="eye-slash">
                            Unpublish Selected
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="bulkDelete" wire:confirm="Are you sure you want to delete the selected posts?" icon="trash">
                            Delete Selected
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Posts Table -->
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="pr-2">
                    <flux:checkbox wire:model.live="selectAll" />
                </flux:table.column>
                <flux:table.column class="max-md:hidden">ID</flux:table.column>
                <flux:table.column><span class="max-md:hidden">Title</span><div class="md:hidden w-6"></div></flux:table.column>
                <flux:table.column class="max-md:hidden">Status</flux:table.column>
                <flux:table.column class="max-lg:hidden">Published At</flux:table.column>
                <flux:table.column class="max-md:hidden">Author</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($posts as $post)
                    <flux:table.row>
                         <flux:table.cell class="pr-2"><flux:checkbox wire:model.live="selectedPosts" value="{{ $post->id }}" /></flux:table.cell>
                        <flux:table.cell class="max-md:hidden">#{{ $post->id }}</flux:table.cell>
                        <flux:table.cell class="min-w-6">
                            <div class="space-y-1">
                                <div class="font-medium">{{ $post->title }}</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 max-md:hidden">
                                    {{ Str::limit(strip_tags($post->content), 60) }}
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="max-md:hidden">
                            @if($post->published_at)
                                <flux:badge color="green" size="sm" inset="top bottom">Published</flux:badge>
                            @else
                                <flux:badge color="yellow" size="sm" inset="top bottom">Draft</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="max-lg:hidden">{{ $post->published_at?->format('M d, Y') ?? 'Not Published' }}</flux:table.cell>
                        <flux:table.cell class="max-md:hidden">
                            <div class="flex items-center gap-2">
                                <flux:avatar src="https://i.pravatar.cc/48?img={{ $post->user_id }}" size="xs" />
                                <span>{{ $post->user->name }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown position="bottom" align="end" offset="-15">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" :href="route('admin.posts.edit', $post)">Edit</flux:menu.item>
                                    <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this post?">Delete</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                 @empty
                     <flux:table.row>
                         <flux:table.cell colspan="7">
                             <div class="text-center py-16">
                                 <div class="w-20 h-20 mx-auto bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-6">
                                     <flux:icon name="document-text" class="h-10 w-10" />
                                 </div>
                                 <flux:heading size="lg" class="mb-2">No posts yet</flux:heading>
                                 <flux:subheading class="mb-8 max-w-sm mx-auto">Get started by creating your first blog post and sharing your thoughts with the world.</flux:subheading>
                                 <flux:button :href="route('admin.posts.create')" variant="primary" icon="plus">
                                     Create Your First Post
                                 </flux:button>
                             </div>
                         </flux:table.cell>
                     </flux:table.row>
                 @endforelse
             </flux:table.rows>
         </flux:table>

         @if($posts->hasPages())
             <flux:pagination :paginator="$posts" />
         @endif
    </div>
</div>
