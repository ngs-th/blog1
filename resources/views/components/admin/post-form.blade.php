@props([
    'post' => null,
    'title' => '',
    'content' => '',
    'published_at' => null,
    'submitText' => 'Save Post',
    'submitAction' => 'save',
    'showStatus' => false
])

<form wire:submit.prevent="{{ $submitAction }}" class="space-y-6">
    <!-- Main Content Card -->
    <flux:card>
        <flux:heading size="lg" class="mb-6">Post Content</flux:heading>

        <div class="space-y-6">
            <!-- Title Field -->
            <flux:field>
                <flux:label>Post Title *</flux:label>
                <flux:input 
                    wire:model="title" 
                    placeholder="Enter an engaging title for your post"
                    :error="$errors->first('title')"
                />
                <flux:error name="title" />
            </flux:field>

            <!-- Content Field -->
            <flux:field>
                <flux:label>Content *</flux:label>
                <flux:textarea 
                    wire:model="content" 
                    rows="15"
                    placeholder="Write your post content here..."
                    :error="$errors->first('content')"
                />
                <flux:error name="content" />
                <flux:description>
                    Tip: You can use line breaks to separate paragraphs
                </flux:description>
            </flux:field>
        </div>
    </flux:card>

    <!-- Publication Settings Card -->
    <flux:card>
        <flux:heading size="lg" class="mb-4">Publication Settings</flux:heading>

        <div class="space-y-4">
            <flux:field>
                <flux:label>Publication Date & Time</flux:label>
                <flux:input 
                    type="datetime-local"
                    wire:model="published_at"
                    placeholder="Select publication date and time"
                    :invalid="$errors->has('published_at')"
                />
                <flux:error name="published_at" />
                <flux:description>
                    Leave empty to save as draft. Set a future date to schedule publication.
                </flux:description>
            </flux:field>

            @if($showStatus && $post)
                <!-- Current Status Display -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                        @if($post->published_at)
                            <flux:badge variant="success">Published</flux:badge>
                            <span class="text-sm text-gray-600">
                                Since {{ $post->published_at->format('M j, Y') }}
                            </span>
                        @else
                            <flux:badge variant="warning">Draft</flux:badge>
                            <span class="text-sm text-gray-600">
                                Created {{ $post->created_at->format('M j, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Action Buttons -->
    <flux:card>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <flux:button 
                :href="route('admin.posts.index')"
                variant="ghost"
                class="w-full sm:w-auto"
            >
                Cancel
            </flux:button>

            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <flux:button
                    type="button"
                    wire:click="$set('published_at', null)"
                    variant="outline"
                    onclick="document.getElementById('published_at').value = ''"
                    class="w-full sm:w-auto"
                >
                    Save as Draft
                </flux:button>
                
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="check"
                    class="w-full sm:w-auto"
                >
                    {{ $submitText }}
                </flux:button>
            </div>
        </div>
    </flux:card>
</form>