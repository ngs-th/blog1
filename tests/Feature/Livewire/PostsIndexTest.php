<?php

use App\Livewire\Posts\Index;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Clear cache before each test to prevent interference
    Cache::flush();
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Posts Index Component', function () {
    test('can render posts index component', function () {
        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.posts.index');
    });

    test('displays published posts', function () {
        $publishedPost = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Published Post',
            'published_at' => now(),
        ]);

        $unpublishedPost = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Unpublished Post',
            'published_at' => null,
        ]);

        Livewire::test(Index::class)
            ->assertSee('Published Post')
            ->assertDontSee('Unpublished Post');
    });

    test('can search posts by title', function () {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Laravel Testing Guide',
            'published_at' => now(),
        ]);

        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Vue.js Components',
            'published_at' => now(),
        ]);

        Livewire::test(Index::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Testing Guide')
            ->assertDontSee('Vue.js Components');
    });

    test('can filter posts by author', function () {
        $author1 = User::factory()->create(['name' => 'John Doe']);
        $author2 = User::factory()->create(['name' => 'Jane Smith']);

        Post::factory()->create([
            'user_id' => $author1->id,
            'title' => 'Post by John',
            'published_at' => now(),
        ]);

        Post::factory()->create([
            'user_id' => $author2->id,
            'title' => 'Post by Jane',
            'published_at' => now(),
        ]);

        Livewire::test(Index::class)
            ->set('authorFilter', 'John Doe')
            ->assertSee('Post by John')
            ->assertDontSee('Post by Jane');
    });

    test('can sort posts by different criteria', function () {
        $oldPost = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Old Post',
            'published_at' => now()->subDays(5),
        ]);

        $newPost = Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'New Post',
            'published_at' => now(),
        ]);

        // Test latest first (default)
        $component = Livewire::test(Index::class)
            ->set('sortBy', 'latest');

        $posts = $component->viewData('posts');
        expect($posts->first()->title)->toBe('New Post');

        // Test oldest first
        $component->set('sortBy', 'oldest');
        $posts = $component->viewData('posts');
        expect($posts->first()->title)->toBe('Old Post');
    });

    test('can toggle like status', function () {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $component = Livewire::test(Index::class);

        // Initially not liked
        expect(session()->get("liked_posts.{$post->id}", false))->toBeFalse();

        // Toggle like
        $component->call('likePost', $post->id)
            ->assertDispatched('post-action');
        expect(session()->get("liked_posts.{$post->id}", false))->toBeTrue();

        // Toggle unlike
        $component->call('likePost', $post->id)
            ->assertDispatched('post-action');
        expect(session()->get("liked_posts.{$post->id}", false))->toBeFalse();
    });

    test('can toggle bookmark status', function () {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $component = Livewire::test(Index::class);

        // Initially not bookmarked
        expect(session()->get("bookmarked_posts.{$post->id}", false))->toBeFalse();

        // Toggle bookmark
        $component->call('bookmarkPost', $post->id)
            ->assertDispatched('post-action');
        expect(session()->get("bookmarked_posts.{$post->id}", false))->toBeTrue();

        // Toggle unbookmark
        $component->call('bookmarkPost', $post->id)
            ->assertDispatched('post-action');
        expect(session()->get("bookmarked_posts.{$post->id}", false))->toBeFalse();
    });

    test('can share post', function () {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now(),
        ]);

        Livewire::test(Index::class)
            ->call('sharePost', $post->id)
            ->assertDispatched('copy-to-clipboard');
    });

    test('pagination works correctly', function () {
        // Create more posts than the pagination limit
        Post::factory(25)->create([
            'user_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $component = Livewire::test(Index::class);
        $posts = $component->viewData('posts');

        // Should be paginated (assuming 12 per page)
        expect($posts->count())->toBeLessThanOrEqual(12);
        expect($posts->hasPages())->toBeTrue();
    });

    test('search persists in url', function () {
        Livewire::test(Index::class)
            ->set('search', 'test query')
            ->assertSet('search', 'test query');
    });

    test('filters can be cleared', function () {
        Livewire::test(Index::class)
            ->set('search', 'test')
            ->set('authorFilter', 1)
            ->set('sortBy', 'oldest')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('authorFilter', '')
            ->assertSet('sortBy', 'latest');
    });

    test('shows loading state during search', function () {
        Livewire::test(Index::class)
            ->set('search', 'test')
            ->assertSet('search', 'test');
    });
});

describe('Posts Index Authorization', function () {
    test('guest users can view published posts', function () {
        auth()->logout();

        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Public Post',
            'published_at' => now(),
        ]);

        Livewire::test(Index::class)
            ->assertSee('Public Post');
    });

    test('shows appropriate content for authenticated users', function () {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now(),
        ]);

        Livewire::test(Index::class)
            ->assertSee($post->title)
            ->assertSee('Like')
            ->assertSee('Bookmark')
            ->assertSee('Share');
    });
});
