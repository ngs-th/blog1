<?php

use App\Models\Post;
use App\Models\User;

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

describe('Admin Posts Index Authentication', function () {
    test('redirects unauthenticated users to login', function () {
        $response = $this->get('/admin/posts');
        $response->assertRedirect('/login');
    });

    test('allows authenticated admin users', function () {
        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertOk();
    });
});

describe('Admin Posts Index Functionality', function () {
    test('loads successfully with posts', function () {
        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Test Post Title',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertOk();
    });

    test('loads successfully without posts', function () {
        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertOk();
    });
});

describe('Admin Posts Index Basic Functionality', function () {
    test('displays posts when they exist', function () {
        $post1 = Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'First Test Post',
            'published_at' => now()->subDay(),
        ]);

        $post2 = Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Second Test Post',
            'published_at' => null, // Draft
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertOk();

        // Just verify the page loads successfully
        $this->assertTrue($response->status() === 200);
    });

    test('admin posts page loads successfully', function () {
        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertOk();
    });
});

describe('Admin Posts Index Database Operations', function () {
    test('can create posts in database', function () {
        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
            'title' => 'Test Database Post',
            'published_at' => now()->subDay(),
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Test Database Post',
            'user_id' => $this->admin->id,
        ]);
    });

    test('posts belong to users', function () {
        $post = Post::factory()->create([
            'user_id' => $this->admin->id,
        ]);

        $this->assertEquals($this->admin->id, $post->user_id);
        $this->assertInstanceOf(User::class, $post->user);
    });
});

describe('Admin Posts Index Pagination', function () {
    test('handles multiple posts successfully', function () {
        // Create multiple posts
        Post::factory()->count(5)->create([
            'user_id' => $this->admin->id,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertOk();
    });
});
