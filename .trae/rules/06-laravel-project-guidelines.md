# Project Rules - Laravel Blog Application

This document contains the comprehensive project rules and guidelines for this Laravel application with FluxUI integration.

## Table of Contents

1. [Laravel Boost Guidelines](#laravel-boost-guidelines)
2. [Application Structure](#application-structure)
3. [Frontend & Bundling](#frontend--bundling)
4. [PHP Development Rules](#php-development-rules)
5. [Laravel Core Rules](#laravel-core-rules)
6. [FluxUI Integration](#fluxui-integration)
7. [Livewire Development](#livewire-development)
8. [Livewire Volt](#livewire-volt)
9. [Code Formatting](#code-formatting)
10. [Testing Guidelines](#testing-guidelines)
11. [Tailwind CSS](#tailwind-css)

---

## Laravel Boost Guidelines

### Package Versions
- **Laravel Framework**: v11.31.0
- **FluxUI**: v1.0.10
- **Livewire**: v3.5.6
- **Livewire Volt**: v1.6.8
- **Pest**: v3.5.1
- **Laravel Pint**: v1.18.1
- **Tailwind CSS**: v4.0.0-alpha.25

### Verification Scripts
- Use `php artisan about` to verify application setup
- Check package versions with `composer show`
- Verify frontend setup with `npm list`

---

## Application Structure

### Directory Organization
```
app/
├── Http/Controllers/     # Traditional controllers
├── Livewire/            # Livewire v3 components (App\Livewire namespace)
├── Models/              # Eloquent models
└── ...

resources/
├── views/
│   ├── components/      # Blade components
│   ├── livewire/        # Livewire component views
│   └── volt/            # Volt single-file components
└── js/                  # Frontend assets

tests/
├── Feature/             # Feature tests
├── Unit/                # Unit tests
└── Browser/             # Pest v4 browser tests
```

### File Naming Conventions
- Use kebab-case for view files: `user-profile.blade.php`
- Use PascalCase for PHP classes: `UserController.php`
- Use camelCase for JavaScript files: `userProfile.js`

---

## Frontend & Bundling

### Vite Configuration
- Use Vite for asset bundling
- Entry point: `resources/js/app.js`
- CSS entry: `resources/css/app.css`

### Asset Management
```php
// In Blade templates
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### Build Commands
```bash
npm run dev      # Development
npm run build    # Production
npm run watch    # Watch mode
```

---

## PHP Development Rules

### Code Style
- Follow PSR-12 coding standards
- Use strict types: `declare(strict_types=1);`
- Use type hints for all parameters and return types
- Use readonly properties when appropriate

### Error Handling
```php
// Prefer specific exceptions
throw new \InvalidArgumentException('Invalid user ID provided');

// Use try-catch for expected failures
try {
    $user = User::findOrFail($id);
} catch (ModelNotFoundException $e) {
    return response()->json(['error' => 'User not found'], 404);
}
```

### Database Queries
```php
// Use query builder for complex queries
$users = DB::table('users')
    ->where('active', true)
    ->orderBy('created_at', 'desc')
    ->paginate(15);

// Use Eloquent for simple operations
$user = User::create($validatedData);
```

---

## Laravel Core Rules

### Routing
```php
// Use resource routes when appropriate
Route::resource('posts', PostController::class);

// Use route model binding
Route::get('/posts/{post}', [PostController::class, 'show']);

// Group related routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
});
```

### Validation
```php
// Use Form Requests for complex validation
class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
        ];
    }
}
```

### Middleware
```php
// Apply middleware in routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Protected routes
});
```

---

## FluxUI Integration

### Component Hierarchy (Priority Order)
1. **FluxUI Pro Components** (when available)
2. **FluxUI Free Components**
3. **Custom Blade Components**
4. **Raw HTML** (avoid when possible)

### Essential HTML to FluxUI Replacements

| HTML Element | FluxUI Component | Usage |
|--------------|------------------|-------|
| `<form>` | `<flux:form>` | All forms |
| `<input>` | `<flux:input>` | Text inputs |
| `<button>` | `<flux:button>` | All buttons |
| `<select>` | `<flux:select>` | Dropdowns |
| `<table>` | `<flux:table>` | Data tables |
| `<div class="card">` | `<flux:card>` | Content containers |
| `<nav>` | `<flux:navigation>` | Navigation menus |
| `<header>` | `<flux:header>` | Page headers |

### Pro Component Usage
```blade
{{-- Data Table Pro --}}
<flux:data-table :rows="$users" :columns="$columns" />

{{-- Command Palette Pro --}}
<flux:command-palette :items="$commands" />

{{-- Date Picker Pro --}}
<flux:date-picker wire:model="selectedDate" />
```

---

## Livewire Development

### Livewire 3 Key Changes
- Use `App\Livewire` namespace (not `App\Http\Livewire`)
- Use `wire:model.live` for real-time updates
- Use `$this->dispatch()` for events (not `emit`)
- Default layout: `components.layouts.app`

### Component Structure
```php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class PostList extends Component
{
    use WithPagination;
    
    public string $search = '';
    
    public function render()
    {
        return view('livewire.post-list', [
            'posts' => Post::where('title', 'like', "%{$this->search}%")
                ->paginate(10)
        ]);
    }
}
```

### Testing Livewire Components
```php
test('can search posts', function () {
    $post = Post::factory()->create(['title' => 'Laravel Tips']);
    
    Livewire::test(PostList::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Tips');
});
```

---

## Livewire Volt

### Creating Volt Components
```bash
php artisan make:volt counter --test --pest
```

### Class-Based Volt Component
```php
<?php

use Livewire\Volt\Component;

new class extends Component {
    public int $count = 0;
    
    public function increment(): void
    {
        $this->count++;
    }
}; ?>

<div>
    <h1>{{ $count }}</h1>
    <flux:button wire:click="increment">+</flux:button>
</div>
```

### Functional Volt Patterns
```php
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<div>
    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search..." />
    
    @foreach($this->products as $product)
        <flux:card>
            <h3>{{ $product->name }}</h3>
            <flux:button wire:click="edit({{ $product->id }})">Edit</flux:button>
        </flux:card>
    @endforeach
</div>
```

---

## Code Formatting

### Laravel Pint
- Run `vendor/bin/pint --dirty` before committing
- Do not use `--test` flag, just run `vendor/bin/pint` to fix issues
- Follows PSR-12 standards automatically

---

## Testing Guidelines

### Pest Testing Framework
- All tests must use Pest syntax
- Create tests: `php artisan make:test --pest ExampleTest`
- Test structure:

```php
test('user can create post', function () {
    $user = User::factory()->create();
    
    $this->actingAs($user)
        ->post('/posts', [
            'title' => 'Test Post',
            'content' => 'Test content'
        ])
        ->assertSuccessful();
        
    expect(Post::where('title', 'Test Post')->exists())->toBeTrue();
});
```

### Running Tests
```bash
php artisan test                              # All tests
php artisan test tests/Feature/PostTest.php   # Specific file
php artisan test --filter="user can create"   # Filter by name
```

### Browser Testing (Pest v4)
```php
test('user can navigate to posts', function () {
    $user = User::factory()->create();
    
    $page = visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Login')
        ->assertSee('Dashboard')
        ->click('Posts')
        ->assertSee('All Posts')
        ->assertNoJavascriptErrors();
});
```

---

## Tailwind CSS

### Tailwind v4 Usage
- Import with `@import "tailwindcss";` (not `@tailwind` directives)
- Use updated utility classes (see replacement table)
- Support dark mode with `dark:` prefix

### Spacing and Layout
```html
<!-- Use gap utilities for spacing -->
<div class="flex gap-4">
    <flux:button>Save</flux:button>
    <flux:button variant="outline">Cancel</flux:button>
</div>

<!-- Responsive design -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Content -->
</div>
```

### Replaced Utilities (v4)
| Deprecated | Replacement |
|------------|-------------|
| `bg-opacity-*` | `bg-black/*` |
| `text-opacity-*` | `text-black/*` |
| `flex-shrink-*` | `shrink-*` |
| `flex-grow-*` | `grow-*` |
| `overflow-ellipsis` | `text-ellipsis` |

---

## Development Workflow

1. **Before Starting**: Run `php artisan about` to verify setup
2. **During Development**: 
   - Use FluxUI components over HTML
   - Write tests for new features
   - Follow Livewire/Volt patterns
3. **Before Committing**:
   - Run `vendor/bin/pint --dirty`
   - Run relevant tests
   - Verify no console errors

## Performance Guidelines

- Use database indexing for frequently queried columns
- Implement proper caching strategies
- Optimize Livewire component updates
- Use lazy loading for images and components
- Monitor query performance with Laravel Debugbar

---

*This document should be referenced for all development decisions and updated as the project evolves.*