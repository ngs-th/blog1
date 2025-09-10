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
     * Render the component
     */
    public function render(): View
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $posts = Post::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->latest()
            ->paginate(10);

        return view('livewire.admin.posts.index', [
            'posts' => $posts,
        ]);
    }
}
