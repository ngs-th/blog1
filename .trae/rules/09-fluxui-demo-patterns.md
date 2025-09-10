# FluxUI Demo Implementation Patterns

Based on analysis of the FluxUI demo files in `resources/views/flux-demo/`, this guide provides proven implementation patterns and best practices.

## Layout Patterns

### 1. Header Structure

**Standard Header Pattern:**
```blade
<flux:header class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    <flux:brand href="#" logo="/img/logo.png" name="App Name" class="max-lg:hidden" />
    <flux:navbar class="max-lg:hidden">
        <!-- Navigation items -->
    </flux:navbar>
    <flux:spacer />
    <flux:profile avatar="/img/user.png" />
</flux:header>
```

**Key Principles:**
- Always include responsive sidebar toggle for mobile
- Use `max-lg:hidden` for desktop-only elements
- Include spacer to push profile to the right
- Consistent dark mode support with dual class patterns

### 2. Sidebar Implementation

**Collapsible Sidebar Pattern:**
```blade
<flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900">
    <flux:sidebar.header>
        <flux:sidebar.brand href="#" logo="/img/logo.png" name="App Name" />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>
    
    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="#" current>Home</flux:sidebar.item>
        <flux:sidebar.item icon="inbox" badge="12" href="#">Inbox</flux:sidebar.item>
    </flux:sidebar.nav>
    
    <flux:sidebar.spacer />
    
    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
    </flux:sidebar.nav>
</flux:sidebar>
```

**Best Practices:**
- Use `sticky` for persistent sidebar
- Include `collapsible="mobile"` for responsive behavior
- Group navigation items logically
- Use spacer to separate primary and secondary navigation

### 3. Main Content Container

**Standard Main Pattern:**
```blade
<flux:main container class="max-w-xl lg:max-w-3xl">
    <flux:heading size="xl">Page Title</flux:heading>
    <flux:separator variant="subtle" class="my-8" />
    <!-- Content -->
</flux:main>
```

## Form Patterns

### 1. Authentication Forms

**Login Form Pattern:**
```blade
<div class="w-80 max-w-80 space-y-6">
    <flux:heading class="text-center" size="xl">Welcome back</flux:heading>
    
    <!-- Social login buttons -->
    <div class="space-y-4">
        <flux:button class="w-full">
            <x-slot name="icon"><!-- SVG icon --></x-slot>
            Continue with Google
        </flux:button>
    </div>
    
    <flux:separator text="or" />
    
    <!-- Form fields -->
    <div class="flex flex-col gap-6">
        <flux:input label="Email" type="email" placeholder="email@example.com" />
        
        <flux:field>
            <div class="mb-3 flex justify-between">
                <flux:label>Password</flux:label>
                <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
            </div>
            <flux:input type="password" placeholder="Your password" />
        </flux:field>
        
        <flux:checkbox label="Remember me for 30 days" />
        <flux:button variant="primary" class="w-full">Log in</flux:button>
    </div>
</div>
```

### 2. Settings Forms

**Two-Column Settings Pattern:**
```blade
<div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
    <div class="w-80">
        <flux:heading size="lg">Section Title</flux:heading>
        <flux:subheading>Section description</flux:subheading>
    </div>
    
    <div class="flex-1 space-y-6">
        <flux:input label="Field Label" description="Field description" />
        <flux:select label="Select Label">
            <flux:select.option>Option 1</flux:select.option>
        </flux:select>
        
        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">Save</flux:button>
        </div>
    </div>
</div>
```

## Data Display Patterns

### 1. Statistics Cards

**Stats Grid Pattern:**
```blade
<div class="flex gap-6 mb-6">
    @foreach ($stats as $stat)
        <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
            <flux:subheading>{{ $stat['title'] }}</flux:subheading>
            <flux:heading size="xl" class="mb-2">{{ $stat['value'] }}</flux:heading>
            
            <div class="flex items-center gap-1 font-medium text-sm @if ($stat['trendUp']) text-green-600 dark:text-green-400 @else text-red-500 dark:text-red-400 @endif">
                <flux:icon :icon="$stat['trendUp'] ? 'arrow-trending-up' : 'arrow-trending-down'" variant="micro" />
                {{ $stat['trend'] }}
            </div>
            
            <div class="absolute top-0 right-0 pr-2 pt-2">
                <flux:button icon="ellipsis-horizontal" variant="subtle" size="sm" />
            </div>
        </div>
    @endforeach
</div>
```

### 2. Data Tables

**Responsive Table Pattern:**
```blade
<flux:table>
    <flux:table.columns>
        <flux:table.column></flux:table.column>
        <flux:table.column class="max-md:hidden">ID</flux:table.column>
        <flux:table.column class="max-md:hidden">Date</flux:table.column>
        <flux:table.column>Customer</flux:table.column>
        <flux:table.column>Amount</flux:table.column>
        <flux:table.column></flux:table.column>
    </flux:table.columns>
    
    <flux:table.rows>
        @foreach ($rows as $row)
            <flux:table.row>
                <flux:table.cell class="pr-2"><flux:checkbox /></flux:table.cell>
                <flux:table.cell class="max-md:hidden">#{{ $row['id'] }}</flux:table.cell>
                <flux:table.cell class="min-w-6">
                    <div class="flex items-center gap-2">
                        <flux:avatar src="{{ $row['avatar'] }}" size="xs" />
                        <span class="max-md:hidden">{{ $row['name'] }}</span>
                    </div>
                </flux:table.cell>
                <flux:table.cell variant="strong">{{ $row['amount'] }}</flux:table.cell>
                <flux:table.cell>
                    <flux:dropdown position="bottom" align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                        <flux:menu>
                            <flux:menu.item icon="document-text">View</flux:menu.item>
                            <flux:menu.item icon="archive-box" variant="danger">Archive</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
```

## Interactive Patterns

### 1. Kanban Board

**Card-Based Layout:**
```blade
<div class="overflow-x-auto -m-6 p-6">
    <div class="flex gap-4">
        @foreach ($columns as $column)
            <div class="rounded-lg w-80 max-w-80 bg-zinc-400/5 dark:bg-zinc-900">
                <div class="px-4 py-4 flex justify-between items-start">
                    <div>
                        <flux:heading>{{ $column['title'] }}</flux:heading>
                        <flux:subheading class="mb-0!">{{ $column['count'] }} tasks</flux:subheading>
                    </div>
                    <flux:button variant="subtle" icon="ellipsis-horizontal" size="sm" />
                </div>
                
                <div class="flex flex-col gap-2 px-2">
                    @foreach ($column['cards'] as $card)
                        <div class="bg-white rounded-lg shadow-xs border border-zinc-200 dark:border-white/10 dark:bg-zinc-800 p-3 space-y-2">
                            <div class="flex gap-2">
                                @foreach ($card['badges'] as $badge)
                                    <flux:badge :color="$badge['color']" size="sm">{{ $badge['title'] }}</flux:badge>
                                @endforeach
                            </div>
                            <flux:heading>{{ $card['title'] }}</flux:heading>
                        </div>
                    @endforeach
                </div>
                
                <div class="px-2 py-2">
                    <flux:button variant="subtle" icon="plus" size="sm" class="w-full justify-start!">New task</flux:button>
                </div>
            </div>
        @endforeach
    </div>
</div>
```

### 2. Q&A Board

**Question Card Pattern:**
```blade
<div class="p-3 sm:p-4 rounded-lg">
    <div class="flex flex-row sm:items-center gap-2">
        <flux:avatar src="{{ $user['avatar'] }}" size="xs" class="shrink-0" />
        
        <div class="flex flex-col gap-0.5 sm:gap-2 sm:flex-row sm:items-center">
            <div class="flex items-center gap-2">
                <flux:heading>{{ $user['name'] }}</flux:heading>
                @if ($user['is_moderator'])
                    <flux:badge color="lime" size="sm" icon="check-badge" inset="top bottom">Moderator</flux:badge>
                @endif
            </div>
            <flux:text class="text-sm">{{ $question['created_at'] }}</flux:text>
        </div>
    </div>
    
    <div class="min-h-2 sm:min-h-1"></div>
    
    <div class="pl-8">
        <flux:text variant="strong">{{ $question['title'] }}</flux:text>
        
        <div class="min-h-2"></div>
        
        <div class="flex items-center gap-0">
            <flux:button wire:click="vote" variant="ghost" size="sm" class="flex items-center gap-2">
                <flux:icon.hand-thumb-up variant="outline" class="size-4 text-zinc-400" />
                <flux:text class="text-sm text-zinc-500 tabular-nums">{{ $question['votes'] }}</flux:text>
            </flux:button>
            
            <flux:dropdown>
                <flux:button icon="ellipsis-horizontal" variant="subtle" size="sm" />
                <flux:menu class="min-w-0">
                    <flux:menu.item icon="pencil-square">Edit</flux:menu.item>
                    <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>
</div>
```

## Responsive Design Patterns

### 1. Breakpoint Classes

**Common Responsive Patterns:**
- `max-lg:hidden` - Hide on mobile/tablet
- `lg:hidden` - Hide on desktop
- `max-md:hidden` - Hide on mobile only
- `sm:flex-row` - Stack on mobile, row on larger screens
- `flex-col lg:flex-row` - Column on mobile, row on desktop

### 2. Mobile-First Navigation

**Responsive Navigation Pattern:**
```blade
<!-- Desktop navbar -->
<flux:navbar class="max-lg:hidden">
    <flux:navbar.item href="#" data-current>Dashboard</flux:navbar.item>
    <flux:navbar.item href="#" badge="32">Orders</flux:navbar.item>
</flux:navbar>

<!-- Mobile sidebar -->
<flux:sidebar collapsible="mobile" class="lg:hidden">
    <flux:sidebar.nav>
        <flux:sidebar.item href="#" data-current>Dashboard</flux:sidebar.item>
        <flux:sidebar.item href="#" badge="32">Orders</flux:sidebar.item>
    </flux:sidebar.nav>
</flux:sidebar>
```

## Component Usage Best Practices

### 1. Button Patterns

**Button Hierarchy:**
- `variant="primary"` - Primary actions (save, submit)
- `variant="filled"` - Secondary important actions
- `variant="ghost"` - Tertiary actions
- `variant="subtle"` - Icon-only or minimal actions

### 2. Icon Usage

**Icon Patterns:**
- Use `variant="micro"` for small icons in text
- Use `variant="outline"` for interactive elements
- Use `variant="solid"` for active states
- Always include descriptive labels for accessibility

### 3. Color Consistency

**Color Patterns:**
- `bg-zinc-50 dark:bg-zinc-900` - Light backgrounds
- `border-zinc-200 dark:border-zinc-700` - Subtle borders
- `text-zinc-500 dark:text-zinc-400` - Secondary text
- Use semantic colors for status (green for success, red for danger)

## Performance Considerations

### 1. Lazy Loading

- Use `wire:loading` states for interactive elements
- Implement optimistic updates where appropriate
- Consider pagination for large data sets

### 2. Image Optimization

- Use appropriate avatar sizes (`xs`, `sm`, `lg`, `xl`)
- Implement proper image loading strategies
- Use placeholder images during loading states

## Accessibility Guidelines

### 1. Semantic Structure

- Use proper heading hierarchy
- Include descriptive labels for form fields
- Provide alternative text for images

### 2. Keyboard Navigation

- Ensure all interactive elements are keyboard accessible
- Use proper focus management in modals and dropdowns
- Implement logical tab order

### 3. Screen Reader Support

- Use semantic HTML elements
- Provide descriptive button labels
- Include status announcements for dynamic content

## Implementation Checklist

- [ ] Responsive design implemented with mobile-first approach
- [ ] Dark mode support included throughout
- [ ] Consistent spacing and typography
- [ ] Proper component hierarchy and variants
- [ ] Accessibility features implemented
- [ ] Loading states and error handling
- [ ] Semantic HTML structure
- [ ] Performance optimizations applied