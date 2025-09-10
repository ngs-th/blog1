<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Post $post;

    /**
     * Mount the component with a post
     */
    public function mount(Post $post): void
    {
        // Ensure the post is published or throw 404
        if (! $post->published_at) {
            abort(404);
        }

        $this->post = $post->load('user');
    }

    // Post action methods
    public function likePost($postId)
    {
        $post = Post::getCachedPost($postId) ?? Post::findOrFail($postId);

        // Toggle like status (simplified implementation)
        $liked = session()->get("liked_posts.{$postId}", false);

        if ($liked) {
            session()->forget("liked_posts.{$postId}");
            $this->dispatch('post-action', [
                'type' => 'unlike',
                'message' => 'Post unliked!',
                'postId' => $postId,
            ]);
        } else {
            session()->put("liked_posts.{$postId}", true);
            $this->dispatch('post-action', [
                'type' => 'like',
                'message' => 'Post liked!',
                'postId' => $postId,
            ]);
        }
    }

    public function bookmarkPost($postId)
    {
        $post = Post::getCachedPost($postId) ?? Post::findOrFail($postId);

        // Toggle bookmark status (simplified implementation)
        $bookmarked = session()->get("bookmarked_posts.{$postId}", false);

        if ($bookmarked) {
            session()->forget("bookmarked_posts.{$postId}");
            $this->dispatch('post-action', [
                'type' => 'unbookmark',
                'message' => 'Bookmark removed!',
                'postId' => $postId,
            ]);
        } else {
            session()->put("bookmarked_posts.{$postId}", true);
            $this->dispatch('post-action', [
                'type' => 'bookmark',
                'message' => 'Post bookmarked!',
                'postId' => $postId,
            ]);
        }
    }

    public function sharePost($postId)
    {
        $post = Post::getCachedPost($postId) ?? Post::findOrFail($postId);
        $url = route('posts.show', $post);

        // Copy URL to clipboard via JavaScript
        $this->dispatch('copy-to-clipboard', [
            'url' => $url,
            'message' => 'Post URL copied to clipboard!',
        ]);
    }

    // Helper method to check if post is liked
    public function isPostLiked($postId)
    {
        return session()->get("liked_posts.{$postId}", false);
    }

    // Helper method to check if post is bookmarked
    public function isPostBookmarked($postId)
    {
        return session()->get("bookmarked_posts.{$postId}", false);
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.posts.show');
    }
}
