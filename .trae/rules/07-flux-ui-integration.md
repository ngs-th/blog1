# 🚀 FluxUI IDE Integration Rules

This document contains IDE-specific snippets, auto-completion patterns, and development workflow optimizations for FluxUI.

> **Note**: For complete component reference, see [01-fluxui-comprehensive-guide.md](01-fluxui-comprehensive-guide.md)
> **Design Patterns**: See [03-fluxui-design-patterns.md](03-fluxui-design-patterns.md) for layout templates

## 🎯 Auto-Complete Triggers

### Smart Suggestions
- Type `form` → Suggest `<flux:form wire:submit="save">`
- Type `input` → Suggest `<flux:input wire:model="property">`
- Type `table` → Suggest `<flux:data-table>` (Pro) first
- Type `editor` → Suggest `<flux:editor wire:model="content">`
- Type `date` → Suggest `<flux:date-picker wire:model="date">`
- Type `modal` → Suggest `<flux:modal wire:model="showModal">`
- Type `button` → Suggest `<flux:button wire:click="action">`
- Type `card` → Suggest `<flux:card>` with nested structure

### Context-Aware Suggestions
- In Livewire components → Always include `wire:` attributes
- In form contexts → Suggest validation patterns
- In data display → Suggest Pro components for advanced features

## 🚀 Advanced Snippet Expansions

### Complete Form Pattern (`flux-form`)
```blade
<flux:form wire:submit="save" class="space-y-6">
    <flux:field>
        <flux:label>Title</flux:label>
        <flux:input wire:model.blur="title" placeholder="Enter title" />
        <flux:error name="title" />
    </flux:field>
    
    <flux:field>
        <flux:label>Content</flux:label>
        <flux:editor wire:model="content" placeholder="Write content..." />
        <flux:error name="content" />
    </flux:field>
    
    <div class="flex justify-end space-x-3">
        <flux:button variant="ghost" wire:click="cancel">Cancel</flux:button>
        <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
            <flux:spinner wire:loading wire:target="save" class="mr-2" />
            Save
        </flux:button>
    </div>
</flux:form>
```

### Pro Data Table Pattern (`flux-datatable`)
```blade
<flux:card>
    <flux:card.header>
        <flux:heading size="lg">Data Management</flux:heading>
    </flux:card.header>
    <flux:card.body class="p-0">
        <flux:data-table 
            :rows="$data" 
            searchable 
            sortable 
            :per-page="20"
            wire:model="tableState"
        >
            <flux:column sortable searchable>Name</flux:column>
            <flux:column sortable>Status</flux:column>
            <flux:column>Actions</flux:column>
        </flux:data-table>
    </flux:card.body>
</flux:card>
```

### Dashboard Layout Pattern (`flux-dashboard`)
```blade
<flux:container size="full">
    <flux:header class="border-b">
        <div class="flex justify-between items-center py-4">
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:avatar src="{{ auth()->user()->avatar }}" size="sm" />
        </div>
    </flux:header>
    
    <div class="flex">
        <flux:sidebar class="w-64 border-r">
            <nav class="p-4 space-y-2">
                <flux:link href="/dashboard" variant="ghost" class="w-full justify-start">
                    <flux:icon name="home" class="mr-3" />
                    Dashboard
                </flux:link>
            </nav>
        </flux:sidebar>
        
        <flux:main class="flex-1 p-6">
            {{-- Content here --}}
        </flux:main>
    </div>
</flux:container>
```

### Modal Dialog Pattern (`flux-modal`)
```blade
<flux:modal wire:model="showModal" variant="flyout">
    <flux:modal.header>
        <flux:heading>Modal Title</flux:heading>
    </flux:modal.header>
    
    <flux:modal.body>
        <flux:text>Modal content goes here.</flux:text>
    </flux:modal.body>
    
    <flux:modal.footer>
        <div class="flex justify-end space-x-3">
            <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
            <flux:button variant="primary" wire:click="confirm">Confirm</flux:button>
        </div>
    </flux:modal.footer>
</flux:modal>
```

### Stats Card Grid Pattern (`flux-stats`)
```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <flux:card>
        <flux:card.body>
            <div class="flex items-center">
                <flux:icon name="users" class="w-8 h-8 text-blue-500" />
                <div class="ml-4">
                    <flux:text size="sm" variant="muted">Total Users</flux:text>
                    <flux:heading size="lg">{{ $totalUsers }}</flux:heading>
                </div>
            </div>
        </flux:card.body>
    </flux:card>
</div>
```

## 🎨 Quick Snippets

### Single Component Snippets
- `fb` → `<flux:button variant="primary">$1</flux:button>`
- `fi` → `<flux:input wire:model="$1" placeholder="$2" />`
- `fs` → `<flux:select wire:model="$1" placeholder="$2"></flux:select>`
- `fc` → `<flux:checkbox wire:model="$1" label="$2" />`
- `ft` → `<flux:textarea wire:model="$1" rows="$2" />`
- `fe` → `<flux:editor wire:model="$1" placeholder="$2" />`
- `fd` → `<flux:date-picker wire:model="$1" />`
- `fdt` → `<flux:data-table :rows="$$1" searchable sortable />`
- `fm` → `<flux:modal wire:model="show$1"></flux:modal>`
- `fsp` → `<flux:spinner wire:loading wire:target="$1" />`

### Livewire Integration Snippets
- `wire:model` → Auto-suggest with `.live`, `.blur`, `.defer` modifiers
- `wire:click` → Auto-suggest with loading states
- `wire:submit` → Auto-suggest with prevent modifier
- `wire:loading` → Auto-suggest with target and class attributes

## ✅ Validation & Best Practices

### Required Patterns
- `<flux:form>` → Must include `wire:submit="methodName"`
- Form inputs → Must include `wire:model` in Livewire components
- Action buttons → Must include `wire:click="action"`
- Modals → Must include `wire:model="stateProperty"`
- Loading states → Include `wire:loading` for better UX

### Pro Component Enforcement
- Rich text → Always suggest `<flux:editor>` over `<flux:textarea>`
- Tables → Always suggest `<flux:data-table>` over `<flux:table>`
- Date inputs → Always suggest `<flux:date-picker>` over `<input type="date">`
- Search → Always suggest `<flux:command-palette>` for complex search

### Performance Optimizations
- Use `wire:model.blur` for non-real-time inputs
- Use `wire:model.live` only when necessary
- Include `wire:loading.attr="disabled"` on submit buttons
- Use `wire:target` for specific loading indicators

### Accessibility Requirements
- Always pair inputs with `<flux:label>`
- Include `<flux:error>` after form fields
- Use proper heading hierarchy with `<flux:heading level="1-6">`
- Include descriptive text with form controls

### Code Quality
- Prefer semantic variants (`primary`, `success`, `warning`, `danger`)
- Use consistent sizing (`sm`, `md`, `lg`, `xl`)
- Include proper spacing classes
- Follow Blade formatting conventions

## Integration Guidelines

### With Livewire
- Always suggest `wire:model` for Flux form components
- Recommend `wire:click` for Flux buttons and interactive elements
- Suggest proper state binding patterns

### With Alpine.js
- Suggest Alpine directives that work well with Flux components
- Recommend proper event handling patterns
- Ensure component reactivity is maintained

### With Tailwind CSS
- Prefer Flux component variants over Tailwind classes
- Suggest Flux design tokens over custom Tailwind utilities
- Recommend component-based styling approach

## Error Prevention

### Common Mistakes to Catch
- Using HTML form elements without proper Flux alternatives
- Missing wire:model on form components
- Incorrect component nesting patterns
- Using CSS classes instead of component variants

### Best Practice Enforcement
- Ensure Pro components are utilized when available
- Check for proper component composition
- Verify responsive design patterns
- Validate accessibility compliance

## Documentation Integration

### Quick Help
- Link to Flux component documentation
- Show component API and available props
- Display usage examples and patterns
- Reference Pro component features

### Code Examples
- Provide context-aware code snippets
- Show before/after HTML to Flux conversions
- Display common component combinations
- Include Livewire integration examples
