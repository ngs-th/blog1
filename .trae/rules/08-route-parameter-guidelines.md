# Laravel Route Parameter Rules

## Overview
This document outlines best practices and rules for handling route parameters in Laravel applications to prevent `UrlGenerationException` errors.

## Common Route Parameter Errors

### 1. Missing Required Parameters
**Error:** `Missing required parameter for [Route: posts.show] [URI: posts/{post}] [Missing parameter: post]`

**Cause:** Route helper called without required parameter or with incorrect parameter.

**Examples of Incorrect Usage:**
```php
// ❌ Missing parameter
route('posts.show')

// ❌ Wrong parameter (using slug when route expects ID)
route('posts.show', $post->slug)

// ❌ Null or undefined variable
route('posts.show', $nonExistentPost)
```

**Correct Usage:**
```php
// ✅ Pass the model instance (Laravel auto-resolves to ID)
route('posts.show', $post)

// ✅ Pass the ID explicitly
route('posts.show', $post->id)

// ✅ Pass array for multiple parameters
route('posts.comments.show', ['post' => $post, 'comment' => $comment])
```

## Prevention Rules

### Rule 1: Always Verify Route Definitions
Before using route helpers, check the route definition:
```bash
php artisan route:list --name=posts.show
```

### Rule 2: Use Model Instances When Possible
```php
// ✅ Preferred - Laravel handles the conversion
route('posts.show', $post)

// ✅ Alternative - explicit ID
route('posts.show', $post->id)
```

### Rule 3: Handle Null Models Safely
```php
// ✅ Check for null before route generation
@if($post)
    <a href="{{ route('posts.show', $post) }}">View Post</a>
@endif

// ✅ Use optional chaining in PHP 8+
{{ $post ? route('posts.show', $post) : '#' }}
```

### Rule 4: Validate Route Parameters in Controllers
```php
public function show(Post $post)
{
    // Laravel automatically handles model binding
    // Throws 404 if post not found
    return view('posts.show', compact('post'));
}
```

### Rule 5: Use Named Parameters for Complex Routes
```php
// ✅ Clear and explicit
route('admin.posts.edit', ['post' => $post->id])

// ✅ Multiple parameters
route('posts.comments.edit', [
    'post' => $post,
    'comment' => $comment
])
```

## Route Model Binding Best Practices

### Default Binding (by ID)
```php
// Route definition
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Controller
public function show(Post $post) {
    // $post is automatically resolved by ID
}
```

### Custom Route Key
```php
// In Post model
public function getRouteKeyName()
{
    return 'slug';
}

// Now routes will use slug instead of ID
route('posts.show', $post) // Uses $post->slug
```

### Explicit Route Key Binding
```php
// Route definition with custom key
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Usage
route('posts.show', $post->slug)
```

## Testing Route Parameters

### Feature Tests
```php
public function test_post_show_route_works()
{
    $post = Post::factory()->create();
    
    $response = $this->get(route('posts.show', $post));
    
    $response->assertStatus(200);
}

public function test_post_show_route_with_invalid_id()
{
    $response = $this->get(route('posts.show', 999999));
    
    $response->assertStatus(404);
}
```

## Debugging Route Issues

### 1. Check Route List
```bash
php artisan route:list
php artisan route:list --name=posts
```

### 2. Debug Route Parameters
```php
// In blade template
@dump(route('posts.show', $post))

// In controller
dd(route('posts.show', $post));
```

### 3. Validate Model Data
```php
// Check if model exists and has required fields
@dump($post)
@dump($post->id)
```

## Recently Fixed Issues

### Issue 1: UrlGenerationException in Edit Page (Fixed)
**Problem:** Route helper using `$post->slug` when route expected post ID
**Error:** `Missing required parameter for [Route: posts.show] [URI: posts/{post}] [Missing parameter: post]`
**Solution:** Changed `route('posts.show', $post->slug)` to `route('posts.show', $post)`
**Files Fixed:** `resources/views/livewire/admin/posts/edit.blade.php`

**Before:**
```php
<flux:button 
    :href="route('posts.show', $post->slug)"
    variant="outline"
    icon="eye"
```

**After:**
```php
<flux:button 
    :href="route('posts.show', $post)"
    variant="outline"
    icon="eye"
```

## Key Takeaways

1. **Always pass the correct parameter type** - Use model instances or IDs, not arbitrary fields
2. **Check route definitions** - Verify what parameters the route expects
3. **Handle null cases** - Always check if models exist before generating routes
4. **Use model binding** - Let Laravel handle the parameter resolution
5. **Test route generation** - Include route tests in your test suite
6. **Debug systematically** - Use route:list and dumps to troubleshoot issues

## Future Development Guidelines

- Always verify route parameters match route definitions
- Use model instances instead of manual field access when possible
- Implement proper null checking in blade templates
- Add route parameter validation in feature tests
- Document custom route key bindings in model comments