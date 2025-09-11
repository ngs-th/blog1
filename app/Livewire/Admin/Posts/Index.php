<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $dateFilter = '30';
    public bool $selectAll = false;
    public array $selectedPosts = [];
    public bool $showStatusFilter = false;
    public bool $showAuthorFilter = false;
    public bool $showMoreFilters = false;

    /**
     * Updated selectAll checkbox
     */
    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedPosts = $this->getPosts()->pluck('id')->toArray();
        } else {
            $this->selectedPosts = [];
        }
    }

    /**
     * Updated selected posts
     */
    public function updatedSelectedPosts(): void
    {
        $this->selectAll = count($this->selectedPosts) === $this->getPosts()->count();
    }

    /**
     * Updated date filter
     */
    public function updatedDateFilter(): void
    {
        $this->resetPage();
        $this->selectedPosts = [];
        $this->selectAll = false;
    }

    /**
     * Bulk publish selected posts
     */
    public function bulkPublish(): void
    {
        if (empty($this->selectedPosts)) {
            return;
        }

        $posts = Post::whereIn('id', $this->selectedPosts)
            ->where('user_id', Auth::id())
            ->get();

        foreach ($posts as $post) {
            $this->authorize('update', $post);
            if (!$post->published_at) {
                $post->update(['published_at' => now()]);
            }
        }

        $this->selectedPosts = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected posts have been published.');
    }

    /**
     * Bulk unpublish selected posts
     */
    public function bulkUnpublish(): void
    {
        if (empty($this->selectedPosts)) {
            return;
        }

        $posts = Post::whereIn('id', $this->selectedPosts)
            ->where('user_id', Auth::id())
            ->get();

        foreach ($posts as $post) {
            $this->authorize('update', $post);
            if ($post->published_at) {
                $post->update(['published_at' => null]);
            }
        }

        $this->selectedPosts = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected posts have been unpublished.');
    }

    /**
     * Bulk delete selected posts
     */
    public function bulkDelete(): void
    {
        if (empty($this->selectedPosts)) {
            return;
        }

        $posts = Post::whereIn('id', $this->selectedPosts)
            ->where('user_id', Auth::id())
            ->get();

        foreach ($posts as $post) {
            $this->authorize('delete', $post);
            $post->delete();
        }

        $this->selectedPosts = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected posts have been deleted.');
    }

    /**
     * Toggle status filter
     */
    public function toggleStatusFilter(): void
    {
        $this->showStatusFilter = !$this->showStatusFilter;
    }

    /**
     * Toggle author filter
     */
    public function toggleAuthorFilter(): void
    {
        $this->showAuthorFilter = !$this->showAuthorFilter;
    }

    /**
     * Toggle more filters
     */
    public function toggleMoreFilters(): void
    {
        $this->showMoreFilters = !$this->showMoreFilters;
    }

    /**
     * Delete a post
     */
    public function delete(Post $post): void
    {
        $this->authorize('delete', $post);

        $post->delete();

        session()->flash('message', 'Post successfully deleted.');
    }

    /**
     * Get posts query
     */
    private function getPosts()
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $query = Post::query()
            ->with('user')
            ->where('user_id', $user->getAuthIdentifier())
            ->latest();

        // Apply date filter
        if ($this->dateFilter !== 'all') {
            $days = (int) $this->dateFilter;
            $query->where('created_at', '>=', now()->subDays($days));
        }

        return $query;
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        $posts = $this->getPosts()->paginate(10);
        
        // Get statistics for all user posts (not filtered by date)
        $user = Auth::user();
        $allUserPosts = Post::where('user_id', $user->getAuthIdentifier());
        
        $totalPosts = $allUserPosts->count();
        $publishedPosts = $allUserPosts->whereNotNull('published_at')->count();
        $draftPosts = $allUserPosts->whereNull('published_at')->count();

        return view('livewire.admin.posts.index', [
            'posts' => $posts,
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'draftPosts' => $draftPosts,
        ]);
    }
}
