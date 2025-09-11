<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Edit extends Component
{
    public Post $post;

    public string $title = '';

    public string $content = '';

    public ?string $published_at = null;

    /** @var array<string, string> */
    protected array $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'published_at' => 'nullable|date',
    ];

    /**
     * Mount the component with a post
     */
    public function mount(Post $post): void
    {
        $this->authorize('update', $post);

        $this->post = $post;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->published_at = $post->published_at?->format("Y-m-d\TH:i");
    }

    /**
     * Save the updated post
     */
    public function save(): void
    {
        $this->authorize('update', $this->post);

        $this->validate();

        $this->post->update([
            'title' => $this->title,
            'content' => $this->content,
            'published_at' => $this->published_at,
        ]);

        session()->flash('message', 'Post successfully updated.');

        $this->redirect(route('admin.posts.index'));
    }

    /**
     * Update the post (alias for save method)
     */
    public function update(): void
    {
        $this->save();
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.admin.posts.edit');
    }
}
