<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    #[Url(as: 'author')]
    public $authorFilter = '';

    #[Url(as: 'sort')]
    public $sortBy = 'latest';

    public $showFilters = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedAuthorFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->authorFilter = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    // Post action methods
    public function likePost($postId)
    {
        $post = Post::findOrFail($postId);

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
        $post = Post::findOrFail($postId);

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
        $post = Post::findOrFail($postId);
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

    public function render()
    {
        // Use cached methods for better performance
        $posts = Post::getCachedPublishedPosts(
            $this->getPage(),
            $this->search,
            $this->authorFilter,
            $this->sortBy,
            12
        );

        // Get cached authors for filter dropdown
        $authors = Post::getCachedAuthors();

        return view('livewire.posts.index', compact('posts', 'authors'));
    }
}
