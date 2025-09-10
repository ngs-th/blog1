<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';

    public string $content = '';

    public ?string $published_at = null;

    public bool $saving = false;

    public bool $autoSaving = false;

    public string $lastSaved = '';

    /** @var array<string, string> */
    protected array $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'published_at' => 'nullable|date',
    ];

    /** @var array<string, string> */
    protected array $messages = [
        'title.required' => 'Please enter a title for your post.',
        'title.max' => 'The title cannot be longer than 255 characters.',
        'content.required' => 'Please add some content to your post.',
        'published_at.date' => 'Please enter a valid date and time.',
    ];

    /**
     * Auto-save draft functionality
     */
    public function autoSave(): void
    {
        if (empty($this->title) && empty($this->content)) {
            return;
        }

        $this->autoSaving = true;
        $this->dispatch('auto-saving');

        try {
            // Validate only non-empty fields for auto-save
            $rules = [];
            if (! empty($this->title)) {
                $rules['title'] = 'string|max:255';
            }
            if (! empty($this->content)) {
                $rules['content'] = 'string';
            }
            if (! empty($this->published_at)) {
                $rules['published_at'] = 'date';
            }

            if (! empty($rules)) {
                $this->validate($rules);
            }

            $this->lastSaved = now()->format('H:i:s');
            $this->dispatch('auto-saved', ['time' => $this->lastSaved]);
        } catch (\Exception $e) {
            // Silently fail auto-save, don't interrupt user
        }

        $this->autoSaving = false;
    }

    /**
     * Save as draft
     */
    public function saveDraft(): void
    {
        $this->saving = true;
        $this->published_at = null;

        try {
            $this->authorize('create', Post::class);

            // Validate with relaxed rules for draft
            $this->validate([
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
            ]);

            $user = Auth::user();
            if (! $user) {
                abort(401);
            }

            /** @var \App\Models\User $user */
            $user->posts()->create([
                'title' => $this->title,
                'content' => $this->content ?: '',
                'published_at' => null,
            ]);

            $this->dispatch('draft-saved');
            session()->flash('message', 'Draft saved successfully.');
            $this->redirect(route('admin.posts.index'));
        } catch (\Exception $e) {
            $this->dispatch('save-error', ['message' => 'Failed to save draft. Please try again.']);
        }

        $this->saving = false;
    }

    /**
     * Save the new post
     */
    public function save(): RedirectResponse
    {
        $this->saving = true;

        try {
            $this->authorize('create', Post::class);

            $this->validate();

            $user = Auth::user();
            if (! $user) {
                abort(401);
            }

            /** @var \App\Models\User $user */
            $user->posts()->create([
                'title' => $this->title,
                'content' => $this->content,
                'published_at' => $this->published_at,
            ]);

            session()->flash('message', 'Post successfully created.');

            return redirect()->route('admin.posts.index');
        } catch (\Exception $e) {
            $this->saving = false;
            $this->dispatch('save-error', ['message' => 'Failed to save post. Please try again.']);
            throw $e;
        }
    }

    /**
     * Handle keyboard shortcuts
     */
    #[On('keyboard-shortcut')]
    public function handleKeyboardShortcut(string $action): void
    {
        match ($action) {
            'save-draft' => $this->saveDraft(),
            'save' => $this->save(),
            default => null,
        };
    }

    /**
     * Real-time validation
     */
    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);

        // Trigger auto-save after a delay (handled by frontend)
        $this->dispatch('field-updated', ['field' => $propertyName]);
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.admin.posts.create');
    }
}
