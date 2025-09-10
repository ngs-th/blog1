<div x-data="postCreateForm()" class="max-w-4xl mx-auto p-6">
    {{-- Page Header - Simplified without flux:header to avoid navbar conflicts --}}
    <div class="mb-8">
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item 
                :href="route('admin.posts.index')" 
                icon="arrow-left"
                wire:navigate
            >
                Posts
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Create New Post</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" class="mb-2 text-gray-900">
                    Create New Post
                </flux:heading>
                <flux:subheading class="text-gray-600">
                    Write and publish your new blog article
                </flux:subheading>
            </div>
            
            {{-- Auto-save Status Indicator --}}
            <div class="flex items-center space-x-2 text-sm">
                <div wire:loading.remove wire:target="autoSave" class="flex items-center space-x-1">
                    @if($lastSaved)
                        <flux:badge variant="success" size="sm">
                            <flux:icon.check class="w-3 h-3" />
                            Auto-saved at {{ $lastSaved }}
                        </flux:badge>
                    @endif
                </div>
                <div wire:loading wire:target="autoSave" class="flex items-center space-x-1">
                    <flux:badge variant="warning" size="sm">
                        <flux:icon.arrow-path class="w-3 h-3 animate-spin" />
                        Saving...
                    </flux:badge>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Form Section --}}
    <main>
        {{-- Enhanced Post Form with UX Improvements --}}
        <form wire:submit.prevent="save" class="space-y-8" x-on:keydown.ctrl.s.prevent="$wire.saveDraft()">
            {{-- Main Content Card --}}
            <flux:card class="overflow-hidden">
                 <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                     <flux:heading size="lg">Post Content</flux:heading>
                     <flux:subheading>The main content of your blog post</flux:subheading>
                 </div>

                <div class="p-6 space-y-8">
                    {{-- Title Field with Character Counter --}}
                    <flux:field>
                        <div class="flex items-center justify-between mb-2">
                            <flux:label class="text-base font-semibold">Post Title *</flux:label>
                            <span class="text-xs text-gray-500" x-text="`${$wire.title.length}/255 characters`"></span>
                        </div>
                        <flux:input 
                            wire:model.live.debounce.500ms="title"
                            placeholder="Enter an engaging title for your post"
                            :error="$errors->first('title')"
                            maxlength="255"
                            class="text-lg font-medium"
                            x-on:input="scheduleAutoSave()"
                        />
                        <flux:error name="title" />
                        <div class="mt-1 flex justify-between text-xs">
                            <flux:description>
                                Choose a compelling title that captures your post's essence
                            </flux:description>
                            <span class="text-gray-400" x-show="$wire.title.length > 200" x-text="`${255 - $wire.title.length} characters remaining`"></span>
                        </div>
                    </flux:field>

                    {{-- Content Field with Word Counter --}}
                    <flux:field>
                        <div class="flex items-center justify-between mb-2">
                            <flux:label class="text-base font-semibold">Content *</flux:label>
                            <div class="flex space-x-4 text-xs text-gray-500">
                                <span x-text="`${$wire.content.split(' ').filter(word => word.length > 0).length} words`"></span>
                                <span x-text="`${$wire.content.length} characters`"></span>
                            </div>
                        </div>
                        <flux:textarea 
                            wire:model.live.debounce.1000ms="content"
                            rows="15"
                            placeholder="Write your post content here..."
                            :error="$errors->first('content')"
                            class="font-mono text-sm"
                            x-on:input="scheduleAutoSave()"
                        />
                        <flux:error name="content" />
                        <flux:description>
                            Tip: Use line breaks to separate paragraphs. Aim for 300+ words for better engagement.
                        </flux:description>
                    </flux:field>
                </div>
            </flux:card>

            {{-- Publication Settings Card --}}
            <flux:card class="overflow-hidden">
                 <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                     <flux:heading size="lg">Publication Settings</flux:heading>
                     <flux:subheading>Control when and how your post is published</flux:subheading>
                 </div>

                <div class="p-6 space-y-6">
                    <flux:field>
                        <flux:label class="text-base font-semibold">Publication Date & Time</flux:label>
                        <flux:input 
                            type="datetime-local"
                            wire:model.live="published_at"
                            :error="$errors->first('published_at')"
                            class="mt-2"
                        />
                        <flux:error name="published_at" />
                        <flux:description class="mt-2">
                            Leave empty to save as draft. Set a future date to schedule publication.
                        </flux:description>
                    </flux:field>
                </div>
            </flux:card>

            {{-- Action Buttons with Loading States --}}
            <flux:card class="overflow-hidden">
                 <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                     <flux:heading size="lg">Actions</flux:heading>
                     <flux:subheading>Save your post or publish it immediately</flux:subheading>
                 </div>
                
                <div class="p-6">
                    {{-- Desktop: Buttons in same row, Mobile: Stacked --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Cancel Button --}}
                            <flux:button 
                                :href="route('admin.posts.index')"
                                variant="ghost"
                                class="w-full sm:w-auto"
                                :disabled="$saving"
                            >
                                <flux:icon.x-mark class="w-4 h-4 mr-1" />
                                Cancel
                            </flux:button>

                            {{-- Save as Draft Button --}}
                            <flux:button
                                type="button"
                                wire:click="saveDraft"
                                variant="outline"
                                class="w-full sm:w-auto"
                                :disabled="$saving || $autoSaving"
                            >
                                <span wire:loading.remove wire:target="saveDraft">
                                    <flux:icon.document class="w-4 h-4 mr-1" />
                                    Save as Draft
                                </span>
                                <span wire:loading wire:target="saveDraft" class="flex items-center">
                                    <flux:icon.arrow-path class="w-4 h-4 mr-1 animate-spin" />
                                    Saving Draft...
                                </span>
                            </flux:button>
                        </div>
                        
                        {{-- Publish Button - Aligned to right on desktop --}}
                        <flux:button
                            type="submit"
                            variant="primary"
                            class="w-full sm:w-auto"
                            :disabled="$saving || $autoSaving"
                        >
                            <span wire:loading.remove wire:target="save">
                                <flux:icon.check class="w-4 h-4 mr-1" />
                                Publish Post
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <flux:icon.arrow-path class="w-4 h-4 mr-1 animate-spin" />
                                Publishing...
                            </span>
                        </flux:button>
                    </div>
                    
                    {{-- Keyboard Shortcuts Help --}}
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <flux:description class="text-xs">
                            💡 <strong>Keyboard shortcuts:</strong> Ctrl+S to save as draft
                        </flux:description>
                    </div>
                </div>
            </flux:card>
        </form>
    </main>

    {{-- Alpine.js Component for Enhanced UX --}}
    <script>
        function postCreateForm() {
            return {
                autoSaveTimeout: null,
                
                init() {
                    // Listen for Livewire events
                    this.$wire.on('auto-saved', (data) => {
                        this.showNotification('Auto-saved at ' + data.time, 'success');
                    });
                    
                    this.$wire.on('draft-saved', () => {
                        this.showNotification('Draft saved successfully!', 'success');
                    });
                    
                    this.$wire.on('save-error', (data) => {
                        this.showNotification(data.message, 'error');
                    });
                    
                    this.$wire.on('field-updated', () => {
                        this.scheduleAutoSave();
                    });
                },
                
                scheduleAutoSave() {
                    clearTimeout(this.autoSaveTimeout);
                    this.autoSaveTimeout = setTimeout(() => {
                        this.$wire.autoSave();
                    }, 3000); // Auto-save after 3 seconds of inactivity
                },
                
                showNotification(message, type) {
                    // You can integrate with your notification system here
                    console.log(`${type.toUpperCase()}: ${message}`);
                }
            }
        }
    </script>
</div>
