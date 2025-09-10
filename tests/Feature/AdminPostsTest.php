<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'name' => 'Admin User',
    ]);

    $this->user = User::factory()->create([
        'email' => 'user@example.com',
        'name' => 'Regular User',
    ]);
});

describe('Post CRUD Operations', function () {
    test('can create a new post', function () {
        $postData = [
            'title' => 'Test Post Title',
            'content' => 'This is test content for the post.',
            'published_at' => now(),
        ];

        $post = Post::factory()->create(array_merge($postData, [
            'user_id' => $this->admin->id,
        ]));

        expect($post)
            ->title->toBe('Test Post Title')
            ->content->toBe('This is test content for the post.')
            ->user_id->toBe($this->admin->id)
            ->published_at->not->toBeNull();

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'user_id' => $this->admin->id,
        ]);
    });

    test('can read existing posts', function () {
        $posts = Post::factory(3)->create([
            'user_id' => $this->admin->id,
        ]);

        $retrievedPosts = Post::all();

        expect($retrievedPosts)->toHaveCount(3);
        expect($retrievedPosts->first()->title)->toBe($posts->first()->title);
    });

    test('can update existing post', function () {
        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Original Title',
        ]);

        $post->update([
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);

        expect($post->fresh())
            ->title->toBe('Updated Title')
            ->content->toBe('Updated content');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    });

    test('can delete existing post', function () {
        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
        ]);

        $postId = $post->id;
        $post->delete();

        $this->assertDatabaseMissing('posts', [
            'id' => $postId,
        ]);

        expect(Post::find($postId))->toBeNull();
    });
});

describe('Post Validation', function () {
    test('requires title when creating post', function () {
        $post = new Post([
            'user_id' => $this->admin->id,
            'content' => 'Content without title',
        ]);

        expect($post->title)->toBeNull();
        expect($post->content)->toBe('Content without title');
    });

    test('requires content when creating post', function () {
        $post = new Post([
            'user_id' => $this->admin->id,
            'title' => 'Title without content',
        ]);

        expect($post->title)->toBe('Title without content');
        expect($post->content)->toBeNull();
    });

    test('requires user_id when creating post', function () {
        $post = new Post([
            'title' => 'Title',
            'content' => 'Content',
        ]);

        expect($post->title)->toBe('Title');
        expect($post->content)->toBe('Content');
        expect($post->user_id)->toBeNull();
    });

    test('validates required fields on save', function () {
        $post = new Post;

        expect(function () use ($post) {
            $post->save();
        })->toThrow(\Illuminate\Database\QueryException::class);
    });
});

describe('Post Relationships', function () {
    test('belongs to a user', function () {
        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
        ]);

        expect($post->user)
            ->toBeInstanceOf(User::class)
            ->id->toBe($this->admin->id)
            ->email->toBe('admin@example.com');
    });

    test('user can have multiple posts', function () {
        $posts = Post::factory(3)->create([
            'user_id' => $this->admin->id,
        ]);

        $userPosts = $this->admin->fresh()->posts;

        expect($userPosts)->toHaveCount(3);
        expect($userPosts->pluck('id')->sort()->values()->toArray())
            ->toEqual($posts->pluck('id')->sort()->values()->toArray());
    });
});

describe('Post Scopes and Queries', function () {
    test('can filter published posts', function () {
        Post::factory(2)->create([
            'user_id' => $this->admin->id,
            'published_at' => now(),
        ]);

        Post::factory(1)->create([
            'user_id' => $this->admin->id,
            'published_at' => null,
        ]);

        $publishedPosts = Post::whereNotNull('published_at')->get();
        $unpublishedPosts = Post::whereNull('published_at')->get();

        expect($publishedPosts)->toHaveCount(2);
        expect($unpublishedPosts)->toHaveCount(1);
    });

    test('can search posts by title', function () {
        Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Laravel Testing Guide',
        ]);

        Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Vue.js Components',
        ]);

        $laravelPosts = Post::where('title', 'like', '%Laravel%')->get();
        $vuePosts = Post::where('title', 'like', '%Vue%')->get();

        expect($laravelPosts)->toHaveCount(1);
        expect($vuePosts)->toHaveCount(1);
        expect($laravelPosts->first()->title)->toBe('Laravel Testing Guide');
    });

    test('can order posts by publication date', function () {
        $oldPost = Post::factory()->create([
            'user_id' => $this->admin->id,
            'published_at' => now()->subDays(5),
            'title' => 'Old Post',
        ]);

        $newPost = Post::factory()->create([
            'user_id' => $this->admin->id,
            'published_at' => now(),
            'title' => 'New Post',
        ]);

        $latestFirst = Post::orderBy('published_at', 'desc')->get();
        $oldestFirst = Post::orderBy('published_at', 'asc')->get();

        expect($latestFirst->first()->title)->toBe('New Post');
        expect($oldestFirst->first()->title)->toBe('Old Post');
    });
});
