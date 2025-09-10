# Coding Rules for Laravel Blog Project

## Flux UI Component Guidelines

### Primary Rule: Flux First
**Always use Flux UI components instead of standard HTML tags whenever possible.**

### Component Hierarchy (Use in this order of preference):
1. **Flux Pro Components** (we have a license - use these extensively)
2. **Flux Standard Components**
3. **Custom Flux Components** (if needed)
4. **Standard HTML** (only as last resort)

## Flux Component Usage Rules

### Layout Components
- Use `<flux:layout>` instead of `<html>`, `<body>` structure
- Use `<flux:header>`, `<flux:sidebar>`, `<flux:main>` for page structure
- Use `<flux:container>` instead of `<div class="container">`

### Navigation Components
- Use `<flux:navbar>` instead of `<nav>`
- Use `<flux:menu>` and `<flux:menu.item>` instead of `<ul>` and `<li>`
- Use `<flux:breadcrumbs>` for navigation trails
- Use `<flux:tabs>` for tabbed interfaces

### Form Components
- Use `<flux:form>` instead of `<form>`
- Use `<flux:input>` instead of `<input>`
- Use `<flux:textarea>` instead of `<textarea>`
- Use `<flux:select>` instead of `<select>`
- Use `<flux:checkbox>` instead of `<input type="checkbox">`
- Use `<flux:radio>` instead of `<input type="radio">`
- Use `<flux:button>` instead of `<button>`
- Use `<flux:field>` for form field grouping with labels

### Content Components
- Use `<flux:card>` instead of `<div class="card">`
- Use `<flux:heading>` instead of `<h1>`, `<h2>`, etc.
- Use `<flux:text>` instead of `<p>`
- Use `<flux:link>` instead of `<a>`
- Use `<flux:badge>` for status indicators
- Use `<flux:avatar>` for user profile images

### Interactive Components
- Use `<flux:modal>` instead of custom modal implementations
- Use `<flux:dropdown>` instead of custom dropdown menus
- Use `<flux:tooltip>` for hover information
- Use `<flux:accordion>` for collapsible content
- Use `<flux:toast>` for notifications

### Data Display Components
- Use `<flux:table>` instead of `<table>`
- Use `<flux:table.row>`, `<flux:table.cell>` for table structure
- Use `<flux:pagination>` for page navigation
- Use `<flux:progress>` for progress indicators

### Pro Components (Priority Usage)
Since we have a Pro license, prioritize these components:
- `<flux:command-palette>` for search interfaces
- `<flux:data-table>` for advanced table functionality
- `<flux:date-picker>` for date inputs
- `<flux:editor>` for rich text editing
- `<flux:chart>` for data visualization
- `<flux:calendar>` for date/event displays
- `<flux:kanban>` for task management
- `<flux:tree>` for hierarchical data

## Implementation Guidelines

### 1. Component Attributes
- Always use Flux component attributes instead of HTML attributes
- Example: Use `:variant="primary"` instead of `class="btn-primary"`
- Use `:size="lg"` instead of `class="large"`

### 2. Styling Approach
- Rely on Flux's built-in variants and modifiers
- Use Flux's design tokens for consistency
- Only add custom CSS when Flux doesn't provide the needed styling

### 3. Accessibility
- Flux components come with built-in accessibility features
- Always use semantic Flux components (e.g., `<flux:button>` for buttons)
- Leverage Flux's ARIA attributes and keyboard navigation

### 4. Responsive Design
- Use Flux's responsive props: `:sm`, `:md`, `:lg`, `:xl`
- Example: `<flux:button :size="{ sm: 'sm', lg: 'lg' }">`

### 5. State Management
- Use Flux's state props: `:loading`, `:disabled`, `:selected`
- Integrate with Livewire properties seamlessly

## Code Examples

### ❌ Avoid (Standard HTML)
```blade
<div class="container">
    <form method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
```

### ✅ Prefer (Flux Components)
```blade
<flux:container>
    <flux:form wire:submit="submit">
        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input type="email" wire:model="email" />
        </flux:field>
        <flux:button type="submit" variant="primary">Submit</flux:button>
    </flux:form>
</flux:container>
```

## File Organization

### Blade Templates
- Use Flux components in all `.blade.php` files
- Create reusable Flux component compositions in `resources/views/components/`
- Follow Flux naming conventions for custom components

### Livewire Components
- Integrate Flux components seamlessly with Livewire properties
- Use Flux's wire directives: `wire:model`, `wire:click`, etc.

## Performance Considerations

1. **Bundle Optimization**: Flux components are optimized and tree-shakeable
2. **Lazy Loading**: Use Flux's lazy loading features for heavy components
3. **Caching**: Leverage Flux's built-in caching mechanisms

## Migration Strategy

### For Existing Code
1. **Audit Phase**: Identify all HTML elements that can be replaced
2. **Priority Replacement**: Start with forms, buttons, and navigation
3. **Testing**: Ensure functionality remains intact after migration
4. **Optimization**: Fine-tune Flux component usage

### New Development
- **Always start with Flux components**
- **Research Pro components first** before implementing custom solutions
- **Document any custom implementations** that couldn't use Flux

## Quality Assurance

### Code Review Checklist
- [ ] Are standard HTML tags replaced with Flux components?
- [ ] Are Pro components utilized where applicable?
- [ ] Is the component usage semantically correct?
- [ ] Are accessibility features properly implemented?
- [ ] Is the responsive design working correctly?

### Testing Requirements
- Test all Flux components across different screen sizes
- Verify accessibility with screen readers
- Ensure proper keyboard navigation
- Test component interactions and state changes

## Resources

- [Flux UI Documentation](https://fluxui.dev/docs)
- [Flux Pro Components](https://fluxui.dev/pro)
- [Livewire Integration Guide](https://fluxui.dev/docs/livewire)
- [Accessibility Guidelines](https://fluxui.dev/docs/accessibility)

---

**Remember**: The goal is to create a consistent, accessible, and maintainable UI using Flux components. When in doubt, check if a Flux component exists before writing custom HTML.