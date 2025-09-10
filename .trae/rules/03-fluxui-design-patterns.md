# FluxUI Design Patterns & Layouts

> **Essential Rule**: Follow consistent design patterns across the application for predictable user experience.

## Table of Contents
1. [Layout Patterns](#layout-patterns)
2. [Navigation Patterns](#navigation-patterns)
3. [Form Patterns](#form-patterns)
4. [Data Display Patterns](#data-display-patterns)
5. [Interactive Patterns](#interactive-patterns)
6. [Content Patterns](#content-patterns)

---

## Layout Patterns

### Admin Dashboard Layout
```blade
<!-- Standard Admin Page Structure -->
<div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <flux:navbar class="border-b bg-white">
        <flux:navbar.brand href="{{ route('dashboard') }}">
            <flux:icon name="newspaper" class="w-6 h-6" />
            Blog Admin
        </flux:navbar.brand>
        
        <flux:navbar.group>
            <flux:navbar.item href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                Dashboard
            </flux:navbar.item>
            <flux:navbar.item href="{{ route('admin.posts.index') }}" :current="request()->routeIs('admin.posts.*')">
                Posts
            </flux:navbar.item>
            <flux:navbar.item href="{{ route('admin.users.index') }}" :current="request()->routeIs('admin.users.*')">
                Users
            </flux:navbar.item>
        </flux:navbar.group>
        
        <flux:navbar.group>
            <flux:dropdown>
                <flux:avatar :src="auth()->user()->avatar" :name="auth()->user()->name" />
                <flux:menu>
                    <flux:menu.item href="{{ route('profile.edit') }}">Profile</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="logout">Logout</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:navbar.group>
    </flux:navbar>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <flux:breadcrumbs class="mb-4">
                <flux:breadcrumbs.item href="{{ route('dashboard') }}">Dashboard</flux:breadcrumbs.item>
                @yield('breadcrumbs')
            </flux:breadcrumbs>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="2xl" class="mb-2">@yield('page-title')</flux:heading>
                    <flux:text variant="muted">@yield('page-description')</flux:text>
                </div>
                <div class="mt-4 sm:mt-0">
                    @yield('page-actions')
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        @yield('content')
    </main>
</div>
```

### Two-Column Layout
```blade
<!-- Content + Sidebar Layout -->
<flux:container size="2xl" class="py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <flux:card>
                <flux:card.header>
                    <flux:heading size="lg">Main Content</flux:heading>
                </flux:card.header>
                <flux:card.body>
                    <!-- Content here -->
                </flux:card.body>
            </flux:card>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <flux:card>
                <flux:card.header>
                    <flux:heading size="md">Quick Actions</flux:heading>
                </flux:card.header>
                <flux:card.body class="space-y-3">
                    <flux:button class="w-full" variant="primary">
                        New Post
                    </flux:button>
                    <flux:button class="w-full" variant="outline">
                        Import Posts
                    </flux:button>
                </flux:card.body>
            </flux:card>
            
            <flux:card>
                <flux:card.header>
                    <flux:heading size="md">Recent Activity</flux:heading>
                </flux:card.header>
                <flux:card.body>
                    <!-- Activity list -->
                </flux:card.body>
            </flux:card>
        </div>
    </div>
</flux:container>
```

### Grid Layout
```blade
<!-- Responsive Card Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($posts as $post)
        <flux:card class="h-full flex flex-col">
            @if($post->featured_image)
                <img 
                    src="{{ $post->featured_image }}" 
                    alt="{{ $post->title }}"
                    class="w-full h-48 object-cover rounded-t-lg"
                >
            @endif
            
            <flux:card.body class="flex-1 flex flex-col">
                <flux:badge variant="outline" class="self-start mb-3">
                    {{ $post->category->name }}
                </flux:badge>
                
                <flux:heading size="md" class="mb-2">
                    {{ $post->title }}
                </flux:heading>
                
                <flux:text class="mb-4 flex-1">
                    {{ $post->excerpt }}
                </flux:text>
                
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $post->author->name }}</span>
                    <span>{{ $post->published_at->format('M j, Y') }}</span>
                </div>
            </flux:card.body>
            
            <flux:card.footer>
                <flux:button href="{{ route('posts.show', $post) }}" class="w-full">
                    Read More
                </flux:button>
            </flux:card.footer>
        </flux:card>
    @endforeach
</div>
```

---

## Navigation Patterns

### Primary Navigation
```blade
<!-- Main Site Navigation -->
<flux:navbar class="border-b">
    <flux:navbar.brand href="{{ route('home') }}">
        <flux:icon name="newspaper" class="w-8 h-8" />
        <span class="ml-2 text-xl font-bold">Blog</span>
    </flux:navbar.brand>
    
    <flux:navbar.group class="hidden md:flex">
        <flux:navbar.item href="{{ route('home') }}" :current="request()->routeIs('home')">
            Home
        </flux:navbar.item>
        <flux:navbar.item href="{{ route('posts.index') }}" :current="request()->routeIs('posts.*')">
            Posts
        </flux:navbar.item>
        <flux:navbar.item href="{{ route('categories.index') }}" :current="request()->routeIs('categories.*')">
            Categories
        </flux:navbar.item>
        <flux:navbar.item href="{{ route('about') }}" :current="request()->routeIs('about')">
            About
        </flux:navbar.item>
    </flux:navbar.group>
    
    <flux:navbar.group>
        @auth
            <flux:dropdown>
                <flux:avatar :src="auth()->user()->avatar" :name="auth()->user()->name" size="sm" />
                <flux:menu>
                    <flux:menu.item href="{{ route('dashboard') }}">Dashboard</flux:menu.item>
                    <flux:menu.item href="{{ route('profile.edit') }}">Profile</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="logout">Logout</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        @else
            <flux:button href="{{ route('login') }}" variant="outline">
                Login
            </flux:button>
            <flux:button href="{{ route('register') }}" variant="primary">
                Sign Up
            </flux:button>
        @endauth
    </flux:navbar.group>
</flux:navbar>
```

### Tabbed Navigation
```blade
<!-- Content Tabs -->
<flux:tabs wire:model="activeTab" class="mb-6">
    <flux:tab name="published" icon="eye">
        Published Posts ({{ $publishedCount }})
    </flux:tab>
    <flux:tab name="draft" icon="pencil">
        Drafts ({{ $draftCount }})
    </flux:tab>
    <flux:tab name="scheduled" icon="clock">
        Scheduled ({{ $scheduledCount }})
    </flux:tab>
</flux:tabs>

<div class="tab-content">
    @if($activeTab === 'published')
        <!-- Published posts content -->
    @elseif($activeTab === 'draft')
        <!-- Draft posts content -->
    @elseif($activeTab === 'scheduled')
        <!-- Scheduled posts content -->
    @endif
</div>
```

### Pagination
```blade
<!-- Standard Pagination -->
<div class="flex items-center justify-between mt-6">
    <flux:text variant="muted">
        Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} results
    </flux:text>
    
    {{ $posts->links() }}
</div>
```

---

## Form Patterns

### Standard Form Layout
```blade
<flux:card>
    <flux:card.header>
        <flux:heading size="lg">{{ $post->exists ? 'Edit Post' : 'Create New Post' }}</flux:heading>
    </flux:card.header>
    
    <flux:form wire:submit="save">
        <flux:card.body class="space-y-6">
            <!-- Title Field -->
            <flux:field>
                <flux:label>Post Title</flux:label>
                <flux:input 
                    wire:model.blur="title" 
                    placeholder="Enter a compelling title"
                    required
                />
                <flux:error name="title" />
            </flux:field>
            
            <!-- Slug Field -->
            <flux:field>
                <flux:label>URL Slug</flux:label>
                <flux:input 
                    wire:model.live.debounce.300ms="slug" 
                    placeholder="auto-generated-from-title"
                />
                <flux:text size="sm" variant="muted">
                    Leave blank to auto-generate from title
                </flux:text>
                <flux:error name="slug" />
            </flux:field>
            
            <!-- Content Field -->
            <flux:field>
                <flux:label>Content</flux:label>
                <flux:editor wire:model="content" placeholder="Write your post content..." />
                <flux:error name="content" />
            </flux:field>
            
            <!-- Category and Tags -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Category</flux:label>
                    <flux:select wire:model="category_id" placeholder="Select a category">
                        @foreach($categories as $category)
                            <flux:option value="{{ $category->id }}">{{ $category->name }}</flux:option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>
                
                <flux:field>
                    <flux:label>Tags</flux:label>
                    <flux:input 
                        wire:model="tags" 
                        placeholder="Enter tags separated by commas"
                    />
                    <flux:text size="sm" variant="muted">
                        Separate multiple tags with commas
                    </flux:text>
                </flux:field>
            </div>
            
            <!-- Publishing Options -->
            <div class="border-t pt-6">
                <flux:heading size="md" class="mb-4">Publishing Options</flux:heading>
                
                <div class="space-y-4">
                    <flux:checkbox wire:model="featured" label="Feature this post" />
                    
                    <flux:field>
                        <flux:label>Publication Date</flux:label>
                        <flux:date-picker wire:model="published_at" />
                        <flux:text size="sm" variant="muted">
                            Leave blank to publish immediately
                        </flux:text>
                    </flux:field>
                </div>
            </div>
        </flux:card.body>
        
        <flux:card.footer>
            <div class="flex items-center justify-between">
                <flux:button 
                    type="button" 
                    variant="outline" 
                    href="{{ route('admin.posts.index') }}"
                >
                    Cancel
                </flux:button>
                
                <div class="flex gap-2">
                    <flux:button 
                        type="button" 
                        variant="outline" 
                        wire:click="saveDraft"
                        wire:loading.attr="disabled"
                    >
                        <flux:spinner wire:loading wire:target="saveDraft" class="mr-2" />
                        Save Draft
                    </flux:button>
                    
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        wire:loading.attr="disabled"
                    >
                        <flux:spinner wire:loading wire:target="save" class="mr-2" />
                        {{ $post->exists ? 'Update Post' : 'Publish Post' }}
                    </flux:button>
                </div>
            </div>
        </flux:card.footer>
    </flux:form>
</flux:card>
```

### Search and Filter Form
```blade
<flux:card class="mb-6">
    <flux:card.body>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <flux:field>
                <flux:label>Search</flux:label>
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search posts..."
                    icon="magnifying-glass"
                    clearable
                />
            </flux:field>
            
            <!-- Status Filter -->
            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="statusFilter" placeholder="All statuses">
                    <flux:option value="published">Published</flux:option>
                    <flux:option value="draft">Draft</flux:option>
                    <flux:option value="scheduled">Scheduled</flux:option>
                </flux:select>
            </flux:field>
            
            <!-- Category Filter -->
            <flux:field>
                <flux:label>Category</flux:label>
                <flux:select wire:model.live="categoryFilter" placeholder="All categories">
                    @foreach($categories as $category)
                        <flux:option value="{{ $category->id }}">{{ $category->name }}</flux:option>
                    @endforeach
                </flux:select>
            </flux:field>
            
            <!-- Date Range -->
            <flux:field>
                <flux:label>Date Range</flux:label>
                <flux:select wire:model.live="dateRange" placeholder="All dates">
                    <flux:option value="today">Today</flux:option>
                    <flux:option value="week">This Week</flux:option>
                    <flux:option value="month">This Month</flux:option>
                    <flux:option value="year">This Year</flux:option>
                </flux:select>
            </flux:field>
        </div>
        
        @if($hasFilters)
            <div class="mt-4 pt-4 border-t">
                <flux:button 
                    wire:click="clearFilters" 
                    variant="outline" 
                    size="sm"
                >
                    Clear All Filters
                </flux:button>
            </div>
        @endif
    </flux:card.body>
</flux:card>
```

---

## Data Display Patterns

### Data Table with Actions
```blade
<flux:card>
    <flux:card.header>
        <div class="flex items-center justify-between">
            <flux:heading size="lg">Posts</flux:heading>
            
            <div class="flex items-center gap-2">
                @if(count($selected) > 0)
                    <flux:dropdown>
                        <flux:button variant="outline">
                            Bulk Actions ({{ count($selected) }})
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item wire:click="bulkPublish">Publish Selected</flux:menu.item>
                            <flux:menu.item wire:click="bulkUnpublish">Unpublish Selected</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item wire:click="bulkDelete" variant="danger">
                                Delete Selected
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                @endif
                
                <flux:button href="{{ route('admin.posts.create') }}" variant="primary">
                    <flux:icon name="plus" class="mr-2" />
                    New Post
                </flux:button>
            </div>
        </div>
    </flux:card.header>
    
    <flux:data-table 
        :rows="$this->posts" 
        :columns="[
            ['key' => 'select', 'label' => '', 'sortable' => false],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'author.name', 'label' => 'Author', 'sortable' => true],
            ['key' => 'category.name', 'label' => 'Category', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false]
        ]"
        wire:model="selected"
        searchable
        sortable
    >
        <flux:table.cell key="select" :row="$row">
            <flux:checkbox wire:model="selected" value="{{ $row->id }}" />
        </flux:table.cell>
        
        <flux:table.cell key="title" :row="$row">
            <div>
                <flux:link href="{{ route('admin.posts.edit', $row) }}" class="font-medium">
                    {{ $row->title }}
                </flux:link>
                @if($row->featured)
                    <flux:badge variant="outline" size="sm" class="ml-2">Featured</flux:badge>
                @endif
            </div>
        </flux:table.cell>
        
        <flux:table.cell key="status" :row="$row">
            <flux:badge variant="{{ $row->status === 'published' ? 'success' : ($row->status === 'draft' ? 'warning' : 'info') }}">
                {{ ucfirst($row->status) }}
            </flux:badge>
        </flux:table.cell>
        
        <flux:table.cell key="published_at" :row="$row">
            {{ $row->published_at?->format('M j, Y') ?? 'Not published' }}
        </flux:table.cell>
        
        <flux:table.cell key="actions" :row="$row">
            <flux:dropdown>
                <flux:button size="sm" variant="ghost">
                    <flux:icon name="ellipsis-horizontal" />
                </flux:button>
                <flux:menu>
                    <flux:menu.item href="{{ route('posts.show', $row) }}" target="_blank">
                        <flux:icon name="eye" class="mr-2" />
                        View
                    </flux:menu.item>
                    <flux:menu.item href="{{ route('admin.posts.edit', $row) }}">
                        <flux:icon name="pencil" class="mr-2" />
                        Edit
                    </flux:menu.item>
                    <flux:menu.item wire:click="duplicate({{ $row->id }})">
                        <flux:icon name="document-duplicate" class="mr-2" />
                        Duplicate
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="delete({{ $row->id }})" variant="danger">
                        <flux:icon name="trash" class="mr-2" />
                        Delete
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:table.cell>
    </flux:data-table>
</flux:card>
```

### Statistics Cards
```blade
<!-- Dashboard Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <flux:card>
        <flux:card.body class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <flux:icon name="document-text" class="w-6 h-6 text-blue-600" />
                </div>
            </div>
            <div class="ml-4">
                <flux:text variant="muted" size="sm">Total Posts</flux:text>
                <flux:heading size="2xl">{{ $stats['total_posts'] }}</flux:heading>
            </div>
        </flux:card.body>
    </flux:card>
    
    <flux:card>
        <flux:card.body class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <flux:icon name="eye" class="w-6 h-6 text-green-600" />
                </div>
            </div>
            <div class="ml-4">
                <flux:text variant="muted" size="sm">Published</flux:text>
                <flux:heading size="2xl">{{ $stats['published_posts'] }}</flux:heading>
            </div>
        </flux:card.body>
    </flux:card>
    
    <flux:card>
        <flux:card.body class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <flux:icon name="pencil" class="w-6 h-6 text-yellow-600" />
                </div>
            </div>
            <div class="ml-4">
                <flux:text variant="muted" size="sm">Drafts</flux:text>
                <flux:heading size="2xl">{{ $stats['draft_posts'] }}</flux:heading>
            </div>
        </flux:card.body>
    </flux:card>
    
    <flux:card>
        <flux:card.body class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <flux:icon name="users" class="w-6 h-6 text-purple-600" />
                </div>
            </div>
            <div class="ml-4">
                <flux:text variant="muted" size="sm">Total Views</flux:text>
                <flux:heading size="2xl">{{ number_format($stats['total_views']) }}</flux:heading>
            </div>
        </flux:card.body>
    </flux:card>
</div>
```

---

## Interactive Patterns

### Confirmation Modals
```blade
<!-- Delete Confirmation Modal -->
<flux:modal wire:model="showDeleteModal" variant="flyout">
    <div class="p-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <flux:icon name="exclamation-triangle" class="w-6 h-6 text-red-600" />
            </div>
            <flux:heading size="lg">Delete Post</flux:heading>
        </div>
        
        <flux:text class="mb-6">
            Are you sure you want to delete "{{ $postToDelete?->title }}"? This action cannot be undone.
        </flux:text>
        
        <div class="flex justify-end gap-3">
            <flux:button 
                variant="outline" 
                wire:click="$set('showDeleteModal', false)"
            >
                Cancel
            </flux:button>
            <flux:button 
                variant="danger" 
                wire:click="confirmDelete"
                wire:loading.attr="disabled"
            >
                <flux:spinner wire:loading wire:target="confirmDelete" class="mr-2" />
                Delete Post
            </flux:button>
        </div>
    </div>
</flux:modal>
```

### Toast Notifications
```blade
<!-- Success Toast -->
@if (session('success'))
    <flux:toast variant="success" timeout="5000">
        <flux:icon name="check-circle" class="mr-2" />
        {{ session('success') }}
    </flux:toast>
@endif

<!-- Error Toast -->
@if (session('error'))
    <flux:toast variant="danger" timeout="0">
        <flux:icon name="exclamation-circle" class="mr-2" />
        {{ session('error') }}
    </flux:toast>
@endif
```

---

## Content Patterns

### Empty States
```blade
<!-- No Posts Found -->
@if($posts->isEmpty())
    <flux:card>
        <flux:card.body class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <flux:icon name="document-text" class="w-8 h-8 text-gray-400" />
            </div>
            
            <flux:heading size="lg" class="mb-2">No posts found</flux:heading>
            
            <flux:text variant="muted" class="mb-6">
                @if($search || $statusFilter || $categoryFilter)
                    No posts match your current filters. Try adjusting your search criteria.
                @else
                    Get started by creating your first blog post.
                @endif
            </flux:text>
            
            @if($search || $statusFilter || $categoryFilter)
                <flux:button wire:click="clearFilters" variant="outline">
                    Clear Filters
                </flux:button>
            @else
                <flux:button href="{{ route('admin.posts.create') }}" variant="primary">
                    <flux:icon name="plus" class="mr-2" />
                    Create Your First Post
                </flux:button>
            @endif
        </flux:card.body>
    </flux:card>
@endif
```

### Loading States
```blade
<!-- Table Loading Skeleton -->
<div wire:loading.delay wire:target="search,statusFilter,categoryFilter">
    <flux:card>
        <flux:card.body>
            @for($i = 0; $i < 5; $i++)
                <div class="animate-pulse flex items-center py-4 border-b last:border-b-0">
                    <div class="w-4 h-4 bg-gray-200 rounded mr-4"></div>
                    <div class="flex-1">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="w-20 h-6 bg-gray-200 rounded"></div>
                </div>
            @endfor
        </flux:card.body>
    </flux:card>
</div>
```

---

## Related Documentation

- [FluxUI Comprehensive Guide](01-fluxui-comprehensive-guide.md)
- [FluxUI Performance & Accessibility](02-fluxui-performance-accessibility.md)
- [Flux UI Integration](07-flux-ui-integration.md)
- [FluxUI Demo Patterns](09-fluxui-demo-patterns.md) - Proven implementation examples from demos

---

*These design patterns ensure consistent, professional, and user-friendly interfaces across the entire application.*