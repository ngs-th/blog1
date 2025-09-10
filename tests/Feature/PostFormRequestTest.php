<?php

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

describe('StorePostRequest Validation', function () {
    test('passes validation with valid data', function () {
        $data = [
            'title' => 'Valid Post Title',
            'content' => 'This is valid content that is longer than 10 characters.',
            'published_at' => now()->toDateTimeString(),
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->passes())->toBeTrue();
    });

    test('fails validation when title is missing', function () {
        $data = [
            'content' => 'Valid content here.',
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('title'))->toBeTrue();
        expect($validator->errors()->first('title'))->toBe('A post title is required.');
    });

    test('fails validation when title is too long', function () {
        $data = [
            'title' => str_repeat('a', 256), // 256 characters
            'content' => 'Valid content here.',
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('title'))->toBeTrue();
        expect($validator->errors()->first('title'))->toBe('The post title cannot exceed 255 characters.');
    });

    test('fails validation when content is missing', function () {
        $data = [
            'title' => 'Valid Title',
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('content'))->toBeTrue();
        expect($validator->errors()->first('content'))->toBe('Post content is required.');
    });

    test('fails validation when content is too short', function () {
        $data = [
            'title' => 'Valid Title',
            'content' => 'Short', // Only 5 characters
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('content'))->toBeTrue();
        expect($validator->errors()->first('content'))->toBe('Post content must be at least 10 characters long.');
    });

    test('fails validation when published_at is invalid date', function () {
        $data = [
            'title' => 'Valid Title',
            'content' => 'Valid content here.',
            'published_at' => 'invalid-date',
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('published_at'))->toBeTrue();
        expect($validator->errors()->first('published_at'))->toBe('The publication date must be a valid date.');
    });

    test('passes validation when published_at is null', function () {
        $data = [
            'title' => 'Valid Title',
            'content' => 'Valid content here.',
            'published_at' => null,
        ];

        $request = new StorePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->passes())->toBeTrue();
    });

    test('authorization passes for authenticated user', function () {
        $this->actingAs($this->user);

        $request = new StorePostRequest;

        expect($request->authorize())->toBeTrue();
    });

    test('authorization fails for guest user', function () {
        $request = new StorePostRequest;

        expect($request->authorize())->toBeFalse();
    });
});

describe('UpdatePostRequest Validation', function () {
    test('passes validation with partial valid data', function () {
        $data = [
            'title' => 'Updated Title',
        ];

        $request = new UpdatePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->passes())->toBeTrue();
    });

    test('passes validation with all valid data', function () {
        $data = [
            'title' => 'Updated Post Title',
            'content' => 'This is updated content that is longer than 10 characters.',
            'published_at' => now()->toDateTimeString(),
        ];

        $request = new UpdatePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->passes())->toBeTrue();
    });

    test('fails validation when provided title is too long', function () {
        $data = [
            'title' => str_repeat('a', 256), // 256 characters
        ];

        $request = new UpdatePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('title'))->toBeTrue();
    });

    test('fails validation when provided content is too short', function () {
        $data = [
            'content' => 'Short', // Only 5 characters
        ];

        $request = new UpdatePostRequest;
        $validator = Validator::make($data, $request->rules(), $request->messages());

        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->has('content'))->toBeTrue();
    });

    test('authorization passes for post owner', function () {
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);

        $request = new UpdatePostRequest;
        $request->setRouteResolver(function () use ($post) {
            return new class($post)
            {
                public function __construct(private $post) {}

                public function parameter($key)
                {
                    return $key === 'post' ? $this->post : null;
                }
            };
        });

        expect($request->authorize())->toBeTrue();
    });

    test('authorization fails for non-owner', function () {
        $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
        $this->actingAs($this->user);

        $request = new UpdatePostRequest;
        $request->setRouteResolver(function () use ($post) {
            return new class($post)
            {
                public function __construct(private $post) {}

                public function parameter($key)
                {
                    return $key === 'post' ? $this->post : null;
                }
            };
        });

        expect($request->authorize())->toBeFalse();
    });

    test('authorization fails for guest user', function () {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $request = new UpdatePostRequest;
        $request->setRouteResolver(function () use ($post) {
            return new class($post)
            {
                public function __construct(private $post) {}

                public function parameter($key)
                {
                    return $key === 'post' ? $this->post : null;
                }
            };
        });

        expect($request->authorize())->toBeFalse();
    });

    test('authorization fails when post is not found', function () {
        $this->actingAs($this->user);

        $request = new UpdatePostRequest;
        $request->setRouteResolver(function () {
            return new class
            {
                public function parameter($key)
                {
                    return null; // No post found
                }
            };
        });

        expect($request->authorize())->toBeFalse();
    });
});
