# Comprehensive Guide to Fixing Failed Tests

This guide outlines a systematic approach to identifying and fixing failed tests in a Laravel/Livewire application using Pest testing framework.

## Important: Browser Tests Removed

**Note**: As of the latest update, all Browser tests (Laravel Dusk tests) have been removed from this project to improve test performance and reduce complexity. The test suite now focuses on:

- **Feature Tests**: Testing HTTP requests, Livewire components, and application logic
- **Unit Tests**: Testing individual classes and methods in isolation
- **Auth Tests**: Testing authentication and authorization flows

If you encounter references to Browser tests in documentation or code, they should be considered outdated.

### Removed Browser Test Files
- `tests/Feature/Browser/PostsFormTest.php`
- `tests/Feature/Browser/PostsInteractionTest.php`
- `tests/Feature/Browser/PostsNavigationTest.php`
- `tests/Browser/` directory (empty)

### Current Test Structure
```
tests/
├── Feature/
│   ├── Auth/
│   ├── Livewire/
│   └── Settings/
├── Unit/
├── Pest.php
└── TestCase.php
```

## Step 1: Initial Test Discovery

### Run Tests with Compact Output
```bash
vendor/bin/pest --compact --no-progress --ci
```

**Purpose**: Get a quick overview of which tests are failing without verbose output that might be overwhelming. The `--no-progress` and `--ci` flags provide cleaner output suitable for CI environments.

### If Output Retrieval Issues Occur
Sometimes terminal output may not display properly. In such cases:

1. **Test individual components**:
   ```bash
   vendor/bin/pest tests/Feature/Livewire/ComponentNameTest.php --compact
   ```

2. **Use verbose mode for specific tests**:
   ```bash
   vendor/bin/pest tests/Feature/Livewire/ComponentNameTest.php -v
   ```

3. **Run individual test methods**:
   ```bash
   vendor/bin/pest tests/Feature/Livewire/ComponentNameTest.php --filter="test method name" -v
   ```

### Handling Command Output Issues
When terminal commands fail to show output or show "Failed to retrieve command output":

1. **Check command status with different parameters**:
   ```bash
   # Try different output character counts
   # Use skip_character_count if output is truncated
   ```

2. **Run the command again with different filters**:
   ```bash
   # Sometimes re-running the same command works
   vendor/bin/pest --filter="specific test name"
   ```

3. **Examine the test file directly**:
   ```bash
   # Look at the test code to understand what might be failing
   # Check for patterns like component method calls
   ```

## Step 2: Systematic Test Analysis

### Identify Failing Tests One by One
When multiple tests might be failing, isolate each test:

```bash
# Test each method individually
vendor/bin/pest tests/Feature/Livewire/ComponentTest.php --filter="can render component" -v
vendor/bin/pest tests/Feature/Livewire/ComponentTest.php --filter="handles user interaction" -v
vendor/bin/pest tests/Feature/Livewire/ComponentTest.php --filter="validates input" -v
```

### Real-world Example: PostsIndexTest Analysis
When fixing PostsIndexTest, we systematically tested each method:

```bash
# Test each functionality individually
vendor/bin/pest --filter="displays published posts"
vendor/bin/pest --filter="can search posts by title"
vendor/bin/pest --filter="can filter posts by author"
vendor/bin/pest --filter="can sort posts by different criteria"
vendor/bin/pest --filter="can toggle like status"  # ❌ This failed
vendor/bin/pest --filter="can toggle bookmark status"  # ❌ This also failed
```

### Document Test Results
Keep track of:
- ✅ Tests that pass
- ❌ Tests that fail
- 🔍 Tests that need investigation

### Use Todo Lists for Complex Fixes
For multiple failing tests, create a systematic plan:

1. **Identify failing like functionality test** - Find which specific test is failing
2. **Fix like test assertion** - Replace component calls with session checks
3. **Check bookmark test** - Verify if it has the same issue
4. **Run full test suite** - Ensure all fixes work together

## Step 3: Common Test Failure Patterns

### Pattern 1: Livewire Component Method Calls

**Problem**: Calling component methods that return values instead of Livewire responses

**Example of Failing Code**:
```php
// ❌ This fails because isPostLiked() returns a boolean, not a Livewire response
expect($component->call('isPostLiked', $post->id))->toBeFalse();

// ❌ This also fails for bookmark functionality
expect($component->call('isPostBookmarked', $post->id))->toBeFalse();
```

**Solution**: Check the underlying data directly
```php
// ✅ Check session data directly for likes
expect(session()->get("liked_posts.{$post->id}", false))->toBeFalse();

// ✅ Check session data directly for bookmarks
expect(session()->get("bookmarked_posts.{$post->id}", false))->toBeFalse();
```

**Real-world Example**: In PostsIndexTest and PostsShowTest, the like and bookmark toggle tests were failing because they were calling component methods that return boolean values. The fix was to check the session data directly:

```php
// Before (failing)
expect($component->call('isPostLiked', $post->id))->toBeFalse();
$component->call('toggleLike', $post->id);
expect($component->call('isPostLiked', $post->id))->toBeTrue();

// After (working)
expect(session()->get("liked_posts.{$post->id}", false))->toBeFalse();
$component->call('toggleLike', $post->id)
    ->assertDispatched('post-liked');
expect(session()->get("liked_posts.{$post->id}", false))->toBeTrue();
```

### Pattern 2: View Content Assertions

**Problem**: Test expects specific text that doesn't match the actual rendered content

**Example of Failing Code**:
```php
// ❌ Test expects "Posts" but view shows "Back to Blog"
$component->assertSee('Posts');
```

**Solution**: Update either the test expectation or the view content
```php
// Option 1: Update the view to match test expectation
// In blade template: change "Back to Blog" to "Posts"

// Option 2: Update test to match current view
$component->assertSee('Back to Blog');
```

### Pattern 3: Authentication and Authorization Issues

**Problem**: Tests failing due to authentication state or permission issues

**Solution**: Ensure proper test setup
```php
// Set up authenticated user
$user = User::factory()->create();
$this->actingAs($user);

// Or test as guest
auth()->logout();
```

## Step 4: Debugging Strategies

### 1. Examine Component Code
Before fixing tests, understand what the component actually does:

```php
// Check the component's methods
public function likePost($postId) {
    // What does this method actually do?
    // Does it return a value or just perform actions?
}

public function isPostLiked($postId) {
    // This returns a boolean - can't be tested with Livewire assertions
    return session()->get("liked_posts.{$postId}", false);
}
```

### 2. Check View Templates
Examine the Blade templates to understand what content is actually rendered:

```blade
{{-- What text is actually displayed? --}}
<button>Back to Blog</button> {{-- vs --}} <button>Posts</button>
```

### 3. Verify Test Setup
Ensure tests have proper setup:

```php
beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
```

## Step 5: Fix Implementation

### For Component Method Issues
1. **Identify the problem**: Component methods that return values can't be tested with Livewire assertions
2. **Find the underlying data source**: Usually session, database, or cache
3. **Test the data source directly**: Use appropriate assertions for the actual data

### For View Content Issues
1. **Decide on the source of truth**: Should the test or the view be updated?
2. **Update accordingly**: Either change the view content or the test expectation
3. **Consider user experience**: Ensure changes make sense from a UX perspective

## Step 6: Verification

### Run Individual Fixed Tests
```bash
vendor/bin/pest tests/Feature/Livewire/ComponentTest.php --filter="fixed test name" -v
```

### Run Complete Test Suite
```bash
vendor/bin/pest tests/Feature/Livewire/ComponentTest.php -v
```

### Verify All Tests Pass
```bash
vendor/bin/pest --compact --no-progress --ci
```

### Complete QA Verification
After fixing individual tests, run the complete quality assurance suite:

```bash
# Run the full QA suite (includes Pest, Pint, PHPStan)
./qa-check.sh

# Or run individual tools
vendor/bin/pest --compact --no-progress --ci
vendor/bin/pint --test
vendor/bin/phpstan analyse
```

### Real-world Example: PostsIndexTest Verification
After fixing the like and bookmark tests:

```bash
# 1. Test individual fixes
vendor/bin/pest --filter="can toggle like status"
vendor/bin/pest --filter="can toggle bookmark status"

# 2. Test the entire component
vendor/bin/pest tests/Feature/Livewire/PostsIndexTest.php

# 3. Run full QA suite
./qa-check.sh
# Result: ✅ 111 tests passed (245 assertions)
```

## Best Practices

1. **Fix one test at a time**: Don't try to fix multiple issues simultaneously
2. **Understand before fixing**: Always examine the component and view code first
3. **Test the right thing**: Test the actual behavior, not implementation details
4. **Maintain consistency**: Ensure fixes align with the overall application design
5. **Document changes**: Keep track of what was changed and why

## Common Livewire Testing Patterns

### Testing Component State
```php
// ✅ Test session data
expect(session()->get('key'))->toBe('value');

// ✅ Test database state
expect(Model::where('condition', true)->count())->toBe(1);

// ✅ Test component properties
expect($component->get('property'))->toBe('value');
```

### Testing Component Actions
```php
// ✅ Test method calls and events
$component->call('methodName', $param)
    ->assertDispatched('event-name');

// ✅ Test view content
$component->assertSee('Expected Text');

// ✅ Test redirects
$component->call('method')->assertRedirect('/path');
```

### Testing User Interactions
```php
// ✅ Test form submissions
$component->set('property', 'value')
    ->call('submit')
    ->assertHasNoErrors();

// ✅ Test validation
$component->set('email', 'invalid')
    ->call('submit')
    ->assertHasErrors(['email']);
```

This systematic approach ensures that test failures are identified, understood, and fixed in a methodical way that maintains code quality and test reliability.