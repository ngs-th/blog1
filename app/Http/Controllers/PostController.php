<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Response
    {
        return response()->view('posts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Http\Response
    {
        return response()->view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): \Illuminate\Http\Response
    {
        // Use cached post for better performance
        $cachedPost = Post::getCachedPost($post->id);

        return response()->view('posts.show', ['post' => $cachedPost]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): \Illuminate\Http\Response
    {
        return response()->view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('posts.show', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('posts.index');
    }
}
