<?php

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->post = Post::factory()->create(['user_id' => $this->user->id]);
    $this->otherPost = Post::factory()->create(['user_id' => $this->otherUser->id]);
    $this->policy = new PostPolicy;
});

describe('Post Authorization Policy', function () {
    describe('viewAny method', function () {
        test('allows any authenticated user to view posts', function () {
            expect($this->policy->viewAny($this->user))->toBeTrue();
            expect($this->policy->viewAny($this->otherUser))->toBeTrue();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('viewAny', Post::class))->toBeTrue();
        });
    });

    describe('view method', function () {
        test('allows any authenticated user to view any post', function () {
            expect($this->policy->view($this->user, $this->post))->toBeTrue();
            expect($this->policy->view($this->user, $this->otherPost))->toBeTrue();
            expect($this->policy->view($this->otherUser, $this->post))->toBeTrue();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('view', $this->post))->toBeTrue();
            expect(Gate::allows('view', $this->otherPost))->toBeTrue();
        });
    });

    describe('create method', function () {
        test('allows any authenticated user to create posts', function () {
            expect($this->policy->create($this->user))->toBeTrue();
            expect($this->policy->create($this->otherUser))->toBeTrue();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('create', Post::class))->toBeTrue();
        });
    });

    describe('update method', function () {
        test('allows post owner to update their post', function () {
            expect($this->policy->update($this->user, $this->post))->toBeTrue();
        });

        test('denies non-owner from updating post', function () {
            expect($this->policy->update($this->otherUser, $this->post))->toBeFalse();
            expect($this->policy->update($this->user, $this->otherPost))->toBeFalse();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('update', $this->post))->toBeTrue();
            expect(Gate::denies('update', $this->otherPost))->toBeTrue();
        });

        test('works with HTTP requests', function () {
            // Test with actual HTTP request to ensure policy is applied
            $this->actingAs($this->user)
                ->get(route('admin.posts.edit', $this->post))
                ->assertStatus(200); // Should be able to access edit page

            // Test unauthorized access
            $this->actingAs($this->otherUser)
                ->get(route('admin.posts.edit', $this->post))
                ->assertStatus(403); // Forbidden
        });
    });

    describe('delete method', function () {
        test('allows post owner to delete their post', function () {
            expect($this->policy->delete($this->user, $this->post))->toBeTrue();
        });

        test('denies non-owner from deleting post', function () {
            expect($this->policy->delete($this->otherUser, $this->post))->toBeFalse();
            expect($this->policy->delete($this->user, $this->otherPost))->toBeFalse();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('delete', $this->post))->toBeTrue();
            expect(Gate::denies('delete', $this->otherPost))->toBeTrue();
        });

        test('works with HTTP requests', function () {
            // Since we don't have delete routes exposed, test with policy directly
            $this->actingAs($this->user);
            expect(Gate::allows('delete', $this->post))->toBeTrue();

            $this->actingAs($this->otherUser);
            expect(Gate::denies('delete', $this->post))->toBeTrue();
        });
    });

    describe('restore method', function () {
        test('allows post owner to restore their post', function () {
            expect($this->policy->restore($this->user, $this->post))->toBeTrue();
        });

        test('denies non-owner from restoring post', function () {
            expect($this->policy->restore($this->otherUser, $this->post))->toBeFalse();
            expect($this->policy->restore($this->user, $this->otherPost))->toBeFalse();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('restore', $this->post))->toBeTrue();
            expect(Gate::denies('restore', $this->otherPost))->toBeTrue();
        });
    });

    describe('forceDelete method', function () {
        test('allows post owner to force delete their post', function () {
            expect($this->policy->forceDelete($this->user, $this->post))->toBeTrue();
        });

        test('denies non-owner from force deleting post', function () {
            expect($this->policy->forceDelete($this->otherUser, $this->post))->toBeFalse();
            expect($this->policy->forceDelete($this->user, $this->otherPost))->toBeFalse();
        });

        test('works with Gate facade', function () {
            $this->actingAs($this->user);
            expect(Gate::allows('forceDelete', $this->post))->toBeTrue();
            expect(Gate::denies('forceDelete', $this->otherPost))->toBeTrue();
        });
    });
});

describe('Post Authorization Integration', function () {
    test('middleware applies authorization correctly', function () {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Test guest access to admin area
        $this->get(route('admin.posts.edit', $post))
            ->assertRedirect(route('login'));

        // Test authorized access
        $this->actingAs($this->user)
            ->get(route('admin.posts.edit', $post))
            ->assertStatus(200);

        // Test unauthorized access
        $this->actingAs($this->otherUser)
            ->get(route('admin.posts.edit', $post))
            ->assertStatus(403);
    });

    test('authorization works with different user roles', function () {
        // Create users with different attributes that might affect authorization
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $regularUser = User::factory()->create(['email' => 'user@example.com']);

        $adminPost = Post::factory()->create(['user_id' => $adminUser->id]);
        $userPost = Post::factory()->create(['user_id' => $regularUser->id]);

        // Test that ownership is the only factor in authorization
        $this->actingAs($adminUser);
        expect(Gate::allows('update', $adminPost))->toBeTrue();
        expect(Gate::denies('update', $userPost))->toBeTrue();

        $this->actingAs($regularUser);
        expect(Gate::allows('update', $userPost))->toBeTrue();
        expect(Gate::denies('update', $adminPost))->toBeTrue();
    });

    test('authorization persists across different request types', function () {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $this->actingAs($this->user);

        // Test GET request to admin edit
        $this->get(route('admin.posts.edit', $post))->assertStatus(200);

        // Test GET request to public post view
        $this->get(route('posts.show', $post->id))->assertStatus(200);

        // Test policy methods directly since routes may not be fully implemented
        expect(Gate::allows('update', $post))->toBeTrue();
        expect(Gate::allows('delete', $post))->toBeTrue();
        expect(Gate::allows('view', $post))->toBeTrue();
    });
});
