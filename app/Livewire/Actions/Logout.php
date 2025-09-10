<?php

namespace App\Livewire\Actions;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        /** @var StatefulGuard $guard */
        $guard = Auth::guard('web');
        $guard->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
