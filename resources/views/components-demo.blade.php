<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <flux:heading size="2xl" class="mb-2">UI Component Library Demo</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">
                Showcase of reusable components built with FluxUI patterns
            </flux:text>
        </div>

        <!-- Stats Cards Section -->
        <div class="mb-12">
            <flux:heading size="xl" class="mb-6">Stats Cards</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-stats-card 
                    title="Total Posts"
                    value="1,234"
                    icon="document-text"
                    trend="up"
                    trend-value="+12%"
                    description="Published articles"
                    variant="primary"
                />
                
                <x-stats-card 
                    title="Active Users"
                    value="5,678"
                    icon="users"
                    trend="up"
                    trend-value="+8%"
                    description="Monthly active users"
                    variant="success"
                />
                
                <x-stats-card 
                    title="Page Views"
                    value="89.2K"
                    icon="eye"
                    trend="down"
                    trend-value="-3%"
                    description="This month"
                    variant="warning"
                />
                
                <x-stats-card 
                    title="Bounce Rate"
                    value="23.4%"
                    icon="arrow-trending-down"
                    trend="neutral"
                    trend-value="0%"
                    description="Average session"
                    variant="danger"
                />
            </div>
        </div>

        <!-- Feature Cards Section -->
        <div class="mb-12">
            <flux:heading size="xl" class="mb-6">Feature Cards</flux:heading>
            
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Vertical Layout</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-feature-card 
                        title="Content Management"
                        description="Create, edit, and manage your blog posts with our intuitive editor. Support for rich text, images, and media."
                icon="pencil"
                        icon-color="blue"
                        href="#"
                        badge="Popular"
                    />
                    
                    <x-feature-card 
                        title="Analytics Dashboard"
                        description="Track your blog's performance with detailed analytics and insights about your readers."
                        icon="chart-bar"
                        icon-color="green"
                        href="#"
                    />
                    
                    <x-feature-card 
                        title="SEO Optimization"
                        description="Built-in SEO tools to help your content rank better in search engines."
                        icon="magnifying-glass"
                        icon-color="purple"
                        href="#"
                        badge="New"
                        badge-variant="success"
                    />
                </div>
            </div>
            
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Horizontal Layout</flux:heading>
                <div class="space-y-4">
                    <x-feature-card 
                        title="User Management"
                        description="Manage user accounts, permissions, and roles with granular control over access levels."
                        icon="users"
                        icon-color="red"
                        layout="horizontal"
                        href="#"
                    />
                    
                    <x-feature-card 
                        title="Comment System"
                        description="Engage with your audience through our built-in commenting system with moderation tools."
                        icon="chat-bubble-left"
                        icon-color="yellow"
                        layout="horizontal"
                        href="#"
                    />
                </div>
            </div>
        </div>

        <!-- Base Cards Section -->
        <div class="mb-12">
            <flux:heading size="xl" class="mb-6">Base Cards</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-base-card variant="default" hover="true">
                    <flux:heading size="lg" class="mb-2">Default Card</flux:heading>
                    <flux:text>Standard card with default styling and hover effects.</flux:text>
                </x-base-card>
                
                <x-base-card variant="outlined" shadow="lg">
                    <flux:heading size="lg" class="mb-2">Outlined Card</flux:heading>
                    <flux:text>Card with outlined border and large shadow.</flux:text>
                </x-base-card>
                
                <x-base-card variant="elevated" rounded="lg">
                    <flux:heading size="lg" class="mb-2">Elevated Card</flux:heading>
                    <flux:text>Card with elevated styling and large border radius.</flux:text>
                </x-base-card>
                
                <x-base-card variant="flat" clickable="true">
                    <flux:heading size="lg" class="mb-2">Flat Clickable</flux:heading>
                    <flux:text>Flat card that's clickable with focus states.</flux:text>
                </x-base-card>
                
                <x-base-card loading="true">
                    <flux:heading size="lg" class="mb-2">Loading Card</flux:heading>
                    <flux:text>This content won't show due to loading state.</flux:text>
                </x-base-card>
                
                <x-base-card disabled="true">
                    <flux:heading size="lg" class="mb-2">Disabled Card</flux:heading>
                    <flux:text>Card in disabled state with reduced opacity.</flux:text>
                </x-base-card>
            </div>
        </div>

        <!-- Notification Cards Section -->
        <div class="mb-12">
            <flux:heading size="xl" class="mb-6">Notification Cards</flux:heading>
            <div class="space-y-4 max-w-2xl">
                <x-notification-card 
                    variant="success"
                    title="Success!"
                    message="Your blog post has been published successfully and is now live."
                    timestamp="2 minutes ago"
                />
                
                <x-notification-card 
                    variant="warning"
                    title="Warning"
                    message="Your storage is almost full. Consider upgrading your plan or removing old files."
                    :dismissible="false"
                >
                    <x-slot name="actions">
                        <flux:button size="sm" variant="outline">Upgrade Plan</flux:button>
                        <flux:button size="sm" variant="ghost">Manage Files</flux:button>
                    </x-slot>
                </x-notification-card>
                
                <x-notification-card 
                    variant="error"
                    title="Error"
                    message="Failed to save your changes. Please check your internet connection and try again."
                    icon="signal-slash"
                />
                
                <x-notification-card 
                    variant="info"
                    message="New features are available! Check out our latest updates in the changelog."
                    timestamp="1 hour ago"
                >
                    <x-slot name="actions">
                        <flux:button size="sm" variant="primary">View Changelog</flux:button>
                    </x-slot>
                </x-notification-card>
            </div>
        </div>

        <!-- Component Sizes Demo -->
        <div class="mb-12">
            <flux:heading size="xl" class="mb-6">Component Sizes</flux:heading>
            
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Stats Card Sizes</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-stats-card 
                        title="Small Size"
                        value="123"
                        icon="star"
                        size="sm"
                        variant="primary"
                    />
                    
                    <x-stats-card 
                        title="Default Size"
                        value="456"
                        icon="star"
                        variant="success"
                    />
                    
                    <x-stats-card 
                        title="Large Size"
                        value="789"
                        icon="star"
                        size="lg"
                        variant="warning"
                    />
                </div>
            </div>
            
            <div class="mb-8">
                <flux:heading size="lg" class="mb-4">Feature Card Sizes</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-feature-card 
                        title="Small Feature"
                        description="Compact feature card with smaller text and icon."
                        icon="bolt"
                        size="sm"
                    />
                    
                    <x-feature-card 
                        title="Default Feature"
                        description="Standard feature card with default sizing for most use cases."
                        icon="bolt"
                    />
                    
                    <x-feature-card 
                        title="Large Feature"
                        description="Prominent feature card with larger text and icon for highlighting important features."
                        icon="bolt"
                        size="lg"
                    />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>