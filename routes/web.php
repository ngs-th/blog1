<?php

use App\Livewire\Admin\Posts\Create as AdminCreate;
use App\Livewire\Admin\Posts\Edit as AdminEdit;
use App\Livewire\Admin\Posts\Index as AdminIndex;
use App\Livewire\Posts\Index;
use App\Livewire\Posts\Show;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', Index::class)->name('home');
Route::get('/posts/{post}', Show::class)->name('posts.show');
Route::view('/components-demo', 'components-demo')
    ->name('components.demo');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/posts', AdminIndex::class)->name('posts.index');
        Route::get('/posts/create', AdminCreate::class)->name('posts.create');
        Route::get('/posts/{post}/edit', AdminEdit::class)->name('posts.edit');
    });
});

require __DIR__.'/auth.php';
