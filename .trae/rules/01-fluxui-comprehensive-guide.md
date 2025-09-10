# FluxUI Comprehensive Guide

> **Essential Rule**: Always use FluxUI components over HTML tags. Prioritize Pro components when available.

## Table of Contents
1. [Component Priority & Hierarchy](#component-priority--hierarchy)
2. [Core Usage Rules](#core-usage-rules)
3. [Component Reference](#component-reference)
4. [Livewire Integration](#livewire-integration)
5. [Implementation Patterns](#implementation-patterns)
6. [Common Mistakes](#common-mistakes)

---

## Component Priority & Hierarchy

### Priority Order (Use in this order of preference)
1. **FluxUI Pro Components** (we have license - use extensively)
2. **FluxUI Standard Components**
3. **Custom FluxUI Extensions**
4. **HTML Tags** (last resort only)

### Critical HTML → FluxUI Replacements

| HTML Element | FluxUI Component | Pro Alternative | Priority |
|--------------|------------------|----------------|----------|
| `<textarea>` | `<flux:textarea>` | `<flux:editor>` | **HIGH** |
| `<input type="date">` | `<flux:input>` | `<flux:date-picker>` | **HIGH** |
| `<table>` | `<flux:table>` | `<flux:data-table>` | **HIGH** |
| `<select>` | `<flux:select>` | `<flux:command-palette>` | **HIGH** |
| `<form>` | `<flux:form>` | - | **HIGH** |
| `<input>` | `<flux:input>` | - | **HIGH** |
| `<button>` | `<flux:button>` | - | **HIGH** |
| `<h1-h6>` | `<flux:heading>` | - | **HIGH** |
| `<div class="card">` | `<flux:card>` | - | **HIGH** |

---

## Core Usage Rules

### 1. Component Naming Convention
- Use `flux:component-name` format (kebab-case)
- **NEVER** use nested component syntax like `flux:card.header`
- All Flux components are flat, not hierarchical

### 2. Valid Component Categories

#### 🔥 Pro Components (Priority Usage)
```blade
<!-- Data Input -->
<flux:editor wire:model="content" /> <!-- Rich text editing -->
<flux:date-picker wire:model="date" /> <!-- Date/time selection -->
<flux:command-palette :items="$items" /> <!-- Search interface -->

<!-- Data Display -->
<flux:data-table :rows="$data" searchable sortable /> <!-- Advanced tables -->
<flux:chart type="line" :data="$chartData" /> <!-- Data visualization -->
<flux:calendar wire:model="selectedDate" /> <!-- Calendar displays -->
<flux:kanban :columns="$columns" /> <!-- Task boards -->
<flux:tree :items="$treeData" /> <!-- Hierarchical data -->
```

#### 📝 Form Components
```blade
<flux:form wire:submit="save">
    <flux:field>
        <flux:label>Title</flux:label>
        <flux:input wire:model="title" placeholder="Enter title" />
        <flux:error name="title" />
    </flux:field>
    
    <flux:field>
        <flux:label>Content</flux:label>
        <flux:textarea wire:model="content" rows="5" />
    </flux:field>
    
    <flux:field>
        <flux:label>Category</flux:label>
        <flux:select wire:model="category" placeholder="Choose category">
            <option value="tech">Technology</option>
            <option value="news">News</option>
        </flux:select>
    </flux:field>
    
    <flux:checkbox wire:model="published" label="Publish now" />
    <flux:button type="submit" variant="primary">Save Post</flux:button>
</flux:form>
```

#### 🎨 Layout Components
```blade
<flux:container size="lg">
    <flux:header>
        <flux:heading size="xl">Blog Dashboard</flux:heading>
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="/">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Posts</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </flux:header>
    
    <flux:main>
        <flux:card>
            <!-- Main content -->
        </flux:card>
    </flux:main>
    
    <flux:sidebar position="right">
        <!-- Sidebar content -->
    </flux:sidebar>
</flux:container>
```

#### 🧭 Navigation Components
```blade
<flux:navbar>
    <flux:navbar.brand href="/">Blog</flux:navbar.brand>
    <flux:menu>
        <flux:menu.item href="/posts">Posts</flux:menu.item>
        <flux:menu.item href="/categories">Categories</flux:menu.item>
    </flux:menu>
</flux:navbar>

<flux:tabs>
    <flux:tab name="published" icon="eye">Published</flux:tab>
    <flux:tab name="draft" icon="pencil">Drafts</flux:tab>
</flux:tabs>
```

#### 📊 Data Display
```blade
<!-- Basic table -->
<flux:table>
    <flux:table.header>
        <flux:table.row>
            <flux:table.cell>Title</flux:table.cell>
            <flux:table.cell>Author</flux:table.cell>
            <flux:table.cell>Status</flux:table.cell>
        </flux:table.row>
    </flux:table.header>
    <flux:table.body>
        @foreach($posts as $post)
        <flux:table.row>
            <flux:table.cell>{{ $post->title }}</flux:table.cell>
            <flux:table.cell>{{ $post->author->name }}</flux:table.cell>
            <flux:table.cell>
                <flux:badge variant="{{ $post->published ? 'success' : 'warning' }}">
                    {{ $post->published ? 'Published' : 'Draft' }}
                </flux:badge>
            </flux:table.cell>
        </flux:table.row>
        @endforeach
    </flux:table.body>
</flux:table>

<!-- Pro data table -->
<flux:data-table 
    :rows="$posts" 
    :columns="$columns" 
    searchable 
    sortable 
    wire:model="selected"
/>
```

#### 🎯 Interactive Components
```blade
<!-- Modal -->
<flux:modal name="confirm-delete" variant="flyout">
    <flux:heading>Confirm Deletion</flux:heading>
    <flux:text>Are you sure you want to delete this post?</flux:text>
    <flux:button wire:click="delete" variant="danger">Delete</flux:button>
    <flux:button wire:click="$dispatch('close-modal', 'confirm-delete')">Cancel</flux:button>
</flux:modal>

<!-- Dropdown -->
<flux:dropdown>
    <flux:button>Actions</flux:button>
    <flux:menu>
        <flux:menu.item wire:click="edit">Edit</flux:menu.item>
        <flux:menu.item wire:click="duplicate">Duplicate</flux:menu.item>
        <flux:menu.item wire:click="delete" variant="danger">Delete</flux:menu.item>
    </flux:menu>
</flux:dropdown>

<!-- Tooltip -->
<flux:tooltip content="This will publish the post immediately">
    <flux:button wire:click="publish">Publish</flux:button>
</flux:tooltip>
```

---

## Livewire Integration

### Data Binding Patterns
```php
// Livewire Component
class PostForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $title = '';
    
    #[Validate('required|string')]
    public string $content = '';
    
    #[Validate('required|exists:categories,id')]
    public ?int $category_id = null;
    
    public bool $published = false;
    
    public function save()
    {
        $this->validate();
        
        Post::create([
            'title' => $this->title,
            'content' => $this->content,
            'category_id' => $this->category_id,
            'published' => $this->published,
        ]);
        
        $this->dispatch('post-saved');
    }
}
```

### Real-time Updates
```blade
<!-- Live search -->
<flux:input 
    wire:model.live.debounce.300ms="search" 
    placeholder="Search posts..." 
    icon="magnifying-glass"
/>

<!-- Live validation -->
<flux:input 
    wire:model.blur="email" 
    type="email" 
    label="Email"
/>
<flux:error name="email" />

<!-- Live toggle -->
<flux:switch 
    wire:model.live="published" 
    label="Publish immediately"
/>
```

### Event Handling
```blade
<!-- Form submission -->
<flux:form wire:submit="save">
    <!-- form fields -->
    <flux:button type="submit" variant="primary">Save</flux:button>
</flux:form>

<!-- Click actions -->
<flux:button wire:click="delete({{ $post->id }})">Delete</flux:button>
<flux:button wire:click="$dispatch('open-modal', 'edit-post')">Edit</flux:button>

<!-- Confirmation -->
<flux:button 
    wire:click="delete" 
    wire:confirm="Are you sure you want to delete this post?"
    variant="danger"
>
    Delete
</flux:button>
```

---

## Implementation Patterns

### Authentication Forms
```blade
<!-- Login Form -->
<flux:card>
    <flux:heading size="lg">Sign In</flux:heading>
    
    <flux:form wire:submit="login">
        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input wire:model="email" type="email" required />
            <flux:error name="email" />
        </flux:field>
        
        <flux:field>
            <flux:label>Password</flux:label>
            <flux:input wire:model="password" type="password" required />
            <flux:error name="password" />
        </flux:field>
        
        <flux:checkbox wire:model="remember" label="Remember me" />
        
        <flux:button type="submit" variant="primary" class="w-full">
            Sign In
        </flux:button>
    </flux:form>
    
    <flux:separator />
    
    <flux:text size="sm" class="text-center">
        Don't have an account? 
        <flux:link href="/register">Sign up</flux:link>
    </flux:text>
</flux:card>
```

### Data Tables with Actions
```blade
<flux:card>
    <flux:card.header>
        <flux:heading>Posts</flux:heading>
        <flux:button wire:click="$dispatch('open-modal', 'create-post')" variant="primary">
            New Post
        </flux:button>
    </flux:card.header>
    
    <!-- Pro version -->
    <flux:data-table 
        :rows="$posts" 
        :columns="[
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'author.name', 'label' => 'Author'],
            ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions']
        ]"
        searchable
        wire:model="selected"
    >
        <flux:table.cell key="actions" :row="$row">
            <flux:dropdown>
                <flux:button size="sm" variant="ghost">Actions</flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="edit({{ $row->id }})">Edit</flux:menu.item>
                    <flux:menu.item wire:click="delete({{ $row->id }})">Delete</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:table.cell>
    </flux:data-table>
</flux:card>
```

---

## Common Mistakes

### ❌ WRONG - Invalid Components (DO NOT USE)
```blade
<!-- Nested component syntax -->
<flux:card.header>Header</flux:card.header>
<flux:card.footer>Footer</flux:card.footer>

<!-- Double colons -->
<flux::button>Button</flux::button>

<!-- Non-existent components -->
<flux:columns>Content</flux:columns>
<flux:grid>Content</flux:grid>
```

### ✅ CORRECT - Proper Usage
```blade
<!-- Use heading inside card -->
<flux:card>
    <flux:heading>Header</flux:heading>
    <p>Content</p>
    <div class="mt-4">
        <flux:button>Action</flux:button>
    </div>
</flux:card>

<!-- Single colon syntax -->
<flux:button>Button</flux:button>

<!-- Use CSS Grid or Flexbox -->
<div class="grid grid-cols-2 gap-4">
    <flux:card>Card 1</flux:card>
    <flux:card>Card 2</flux:card>
</div>
```

### Performance Best Practices
```blade
<!-- Use wire:key for dynamic lists -->
@foreach($posts as $post)
    <flux:card wire:key="post-{{ $post->id }}">
        <!-- content -->
    </flux:card>
@endforeach

<!-- Debounce search inputs -->
<flux:input 
    wire:model.live.debounce.500ms="search" 
    placeholder="Search..."
/>

<!-- Use lazy loading for heavy components -->
<div wire:init="loadPosts">
    @if($postsLoaded)
        <flux:data-table :rows="$posts" />
    @else
        <flux:spinner />
    @endif
</div>
```

---

## Universal Props

### `variant` (Most Components)
```blade
<flux:button variant="primary|secondary|outline|ghost|danger" />
<flux:input variant="filled|outline" />
<flux:modal variant="flyout|center" />
<flux:badge variant="solid|outline|soft" />
<flux:separator variant="subtle|strong" />
```

### `size` (Many Components)
```blade
<flux:button size="xs|sm|md|lg|xl" />
<flux:heading size="xs|sm|md|lg|xl|2xl" />
<flux:avatar size="xs|sm|md|lg|xl" />
<flux:container size="sm|md|lg|xl|2xl" />
```

### `icon` (Heroicons Integration)
```blade
<flux:button icon="magnifying-glass" />
<flux:input icon="envelope" />
<flux:tab icon="cog-6-tooth" />
<flux:badge icon="user" />
<flux:breadcrumbs.item icon="home" />
```

---

## Related Documentation

- [FluxUI Performance & Accessibility](02-fluxui-performance-accessibility.md)
- [FluxUI Design Patterns](03-fluxui-design-patterns.md)
- [Flux UI Integration](07-flux-ui-integration.md)
- [FluxUI Demo Patterns](09-fluxui-demo-patterns.md) - Real-world implementation examples

---

*This comprehensive guide consolidates all FluxUI usage patterns and should be the primary reference for component implementation.*