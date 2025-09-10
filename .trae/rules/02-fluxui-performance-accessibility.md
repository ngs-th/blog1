# FluxUI Performance & Accessibility Guide

> **Essential Rule**: All FluxUI components must meet WCAG 2.1 AA standards and be optimized for performance.

## Table of Contents
1. [Performance Optimization](#performance-optimization)
2. [Accessibility Standards](#accessibility-standards)
3. [Responsive Design](#responsive-design)
4. [Testing Guidelines](#testing-guidelines)

---

## Performance Optimization

### Component Selection Strategy
```blade
<!-- ✅ GOOD: Use appropriate component hierarchy -->
<flux:card>
    <flux:heading>Title</flux:heading>
    <flux:text>Simple content</flux:text>
</flux:card>

<!-- ❌ AVOID: Over-nesting components -->
<flux:container>
    <flux:card>
        <div class="wrapper">
            <flux:text>Simple content</flux:text>
        </div>
    </flux:card>
</flux:container>
```

### Livewire Integration Optimization
```blade
<!-- ✅ GOOD: Efficient wire:model usage -->
<flux:input wire:model.blur="search" placeholder="Search..." />

<!-- ❌ AVOID: Real-time updates for heavy operations -->
<flux:input wire:model.live="heavySearch" placeholder="Search..." />

<!-- ✅ GOOD: Debounced updates -->
<flux:input wire:model.live.debounce.500ms="search" placeholder="Search..." />
```

### Form Performance
```blade
<!-- ✅ Optimized form with validation -->
<flux:form wire:submit="save" class="space-y-4">
    <!-- Use .blur for non-critical fields -->
    <flux:field>
        <flux:label>Name</flux:label>
        <flux:input wire:model.blur="name" />
        <flux:error name="name" />
    </flux:field>
    
    <!-- Use .live only when necessary -->
    <flux:field>
        <flux:label>Slug (auto-generated)</flux:label>
        <flux:input wire:model.live.debounce.300ms="slug" />
    </flux:field>
    
    <!-- Batch validation on submit -->
    <flux:button type="submit" wire:loading.attr="disabled">
        <flux:spinner wire:loading wire:target="save" class="mr-2" />
        Save Post
    </flux:button>
</flux:form>
```

### Data Table Performance
```blade
<!-- ✅ Optimized data table -->
<flux:data-table 
    :rows="$this->posts" 
    :columns="$columns"
    wire:model="selected"
    searchable
    sortable
    lazy
>
    <!-- Use wire:key for dynamic content -->
    <flux:table.cell key="actions" :row="$row" wire:key="actions-{{ $row->id }}">
        <flux:dropdown>
            <flux:button size="sm" variant="ghost">Actions</flux:button>
            <flux:menu>
                <flux:menu.item wire:click="edit({{ $row->id }})">Edit</flux:menu.item>
                <flux:menu.item wire:click="delete({{ $row->id }})">Delete</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:table.cell>
</flux:data-table>
```

### Loading States
```blade
<!-- ✅ Proper loading indicators -->
<div wire:init="loadPosts">
    @if($postsLoaded)
        <flux:data-table :rows="$posts" />
    @else
        <div class="flex justify-center py-8">
            <flux:spinner size="lg" />
            <flux:text class="ml-2">Loading posts...</flux:text>
        </div>
    @endif
</div>

<!-- ✅ Button loading states -->
<flux:button 
    wire:click="save" 
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50"
>
    <flux:spinner wire:loading wire:target="save" class="mr-2" />
    Save Changes
</flux:button>
```

---

## Accessibility Standards

### WCAG 2.1 AA Compliance

#### Color Contrast
```blade
<!-- ✅ CORRECT: High contrast text -->
<flux:text class="text-gray-900">Primary content text</flux:text>
<flux:text class="text-gray-700">Secondary content text</flux:text>

<!-- ❌ INCORRECT: Low contrast text -->
<flux:text class="text-gray-400">Hard to read text</flux:text>
```

#### Keyboard Navigation
```blade
<!-- ✅ Proper focus management -->
<flux:modal wire:model="showModal" focus-trap>
    <flux:heading>Edit Post</flux:heading>
    
    <flux:form wire:submit="save">
        <flux:field>
            <flux:label>Title</flux:label>
            <flux:input wire:model="title" autofocus />
        </flux:field>
        
        <div class="flex gap-2 mt-4">
            <flux:button type="submit">Save</flux:button>
            <flux:button 
                type="button" 
                variant="outline" 
                wire:click="$set('showModal', false)"
            >
                Cancel
            </flux:button>
        </div>
    </flux:form>
</flux:modal>
```

#### Screen Reader Support
```blade
<!-- ✅ Proper ARIA labels -->
<flux:button 
    wire:click="delete({{ $post->id }})" 
    aria-label="Delete post: {{ $post->title }}"
    variant="danger"
>
    <flux:icon name="trash" />
</flux:button>

<!-- ✅ Form field associations -->
<flux:field>
    <flux:label for="post-title">Post Title</flux:label>
    <flux:input 
        id="post-title"
        wire:model="title" 
        aria-describedby="title-help"
    />
    <flux:text id="title-help" size="sm" variant="muted">
        Choose a descriptive title for your post
    </flux:text>
    <flux:error name="title" />
</flux:field>

<!-- ✅ Status announcements -->
<div aria-live="polite" aria-atomic="true">
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif
</div>
```

#### Interactive Elements
```blade
<!-- ✅ Accessible dropdown -->
<flux:dropdown>
    <flux:button aria-haspopup="true" aria-expanded="false">
        Post Actions
        <flux:icon name="chevron-down" class="ml-1" />
    </flux:button>
    <flux:menu role="menu">
        <flux:menu.item role="menuitem" wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item role="menuitem" wire:click="duplicate">Duplicate</flux:menu.item>
        <flux:menu.separator role="separator" />
        <flux:menu.item role="menuitem" wire:click="delete" variant="danger">
            Delete
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
```

---

## Responsive Design

### Mobile-First Approach
```blade
<!-- ✅ Responsive layout -->
<flux:container class="px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        @foreach($posts as $post)
            <flux:card class="h-full">
                <flux:heading size="md" class="mb-2">{{ $post->title }}</flux:heading>
                <flux:text class="mb-4 line-clamp-3">{{ $post->excerpt }}</flux:text>
                
                <div class="mt-auto flex flex-col sm:flex-row gap-2">
                    <flux:button href="{{ route('posts.show', $post) }}" class="flex-1">
                        Read More
                    </flux:button>
                    <flux:button variant="outline" class="flex-1">
                        Share
                    </flux:button>
                </div>
            </flux:card>
        @endforeach
    </div>
</flux:container>
```

### Touch Targets
```blade
<!-- ✅ Adequate touch targets (44px minimum) -->
<flux:button size="lg" class="min-h-[44px] min-w-[44px]">
    <flux:icon name="heart" />
</flux:button>

<!-- ✅ Responsive navigation -->
<flux:navbar class="px-4 sm:px-6">
    <flux:navbar.brand class="flex-shrink-0">
        Blog
    </flux:navbar.brand>
    
    <!-- Mobile menu button -->
    <flux:button 
        class="md:hidden" 
        variant="ghost" 
        wire:click="toggleMobileMenu"
        aria-label="Toggle navigation menu"
    >
        <flux:icon name="bars-3" />
    </flux:button>
    
    <!-- Desktop navigation -->
    <flux:navbar.group class="hidden md:flex">
        <flux:navbar.item href="/posts">Posts</flux:navbar.item>
        <flux:navbar.item href="/about">About</flux:navbar.item>
    </flux:navbar.group>
</flux:navbar>

<!-- Mobile menu -->
@if($showMobileMenu)
    <div class="md:hidden border-t">
        <flux:menu class="p-4">
            <flux:menu.item href="/posts">Posts</flux:menu.item>
            <flux:menu.item href="/about">About</flux:menu.item>
        </flux:menu>
    </div>
@endif
```

### Responsive Tables
```blade
<!-- ✅ Mobile-responsive table -->
<div class="overflow-x-auto">
    <flux:table class="min-w-full">
        <flux:table.header>
            <flux:table.row>
                <flux:table.cell class="whitespace-nowrap">Title</flux:table.cell>
                <flux:table.cell class="hidden sm:table-cell">Author</flux:table.cell>
                <flux:table.cell class="hidden md:table-cell">Created</flux:table.cell>
                <flux:table.cell>Status</flux:table.cell>
                <flux:table.cell class="text-right">Actions</flux:table.cell>
            </flux:table.row>
        </flux:table.header>
        <flux:table.body>
            @foreach($posts as $post)
                <flux:table.row>
                    <flux:table.cell class="font-medium">
                        {{ $post->title }}
                        <!-- Mobile-only author info -->
                        <div class="sm:hidden text-sm text-gray-500">
                            by {{ $post->author->name }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="hidden sm:table-cell">
                        {{ $post->author->name }}
                    </flux:table.cell>
                    <flux:table.cell class="hidden md:table-cell">
                        {{ $post->created_at->format('M j, Y') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="{{ $post->status === 'published' ? 'success' : 'warning' }}">
                            {{ ucfirst($post->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-right">
                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost">
                                <flux:icon name="ellipsis-horizontal" />
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item wire:click="edit({{ $post->id }})">Edit</flux:menu.item>
                                <flux:menu.item wire:click="delete({{ $post->id }})">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.body>
    </flux:table>
</div>
```

---

## Testing Guidelines

### Accessibility Testing
```bash
# Install accessibility testing tools
npm install --save-dev @axe-core/playwright

# Run accessibility tests
php artisan test --filter=AccessibilityTest
```

### Performance Testing
```php
// tests/Feature/PerformanceTest.php
test('post list loads within acceptable time', function () {
    Post::factory(100)->create();
    
    $start = microtime(true);
    
    $this->get('/admin/posts')
        ->assertSuccessful();
    
    $loadTime = microtime(true) - $start;
    
    expect($loadTime)->toBeLessThan(2.0); // 2 seconds max
});

test('data table handles large datasets efficiently', function () {
    Post::factory(1000)->create();
    
    Livewire::test(PostIndex::class)
        ->assertSet('posts.total', 1000)
        ->assertSee('Posts') // Ensure page renders
        ->set('search', 'test')
        ->assertCount('posts.data', 10); // Pagination works
});
```

### Manual Testing Checklist

#### Keyboard Navigation
- [ ] All interactive elements are reachable via Tab key
- [ ] Tab order is logical and intuitive
- [ ] Enter/Space keys activate buttons and links
- [ ] Escape key closes modals and dropdowns
- [ ] Arrow keys navigate within menus and tables

#### Screen Reader Testing
- [ ] All images have appropriate alt text
- [ ] Form fields have associated labels
- [ ] Error messages are announced
- [ ] Status changes are announced
- [ ] Headings create logical document structure

#### Mobile Testing
- [ ] All touch targets are at least 44px
- [ ] Content is readable without horizontal scrolling
- [ ] Interactive elements work with touch
- [ ] Forms are easy to complete on mobile
- [ ] Navigation is accessible on small screens

---

*This guide ensures FluxUI components meet modern accessibility and performance standards while maintaining excellent user experience across all devices.*