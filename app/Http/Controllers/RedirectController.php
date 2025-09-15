<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;

class RedirectController extends Controller
{

    public function handleRoot()
    {

        if (!Auth::check()) {
            
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user || !$user->role) {
            return redirect()->route('login');
        }

        switch (optional($user)->role) {
            case Role::Admin:
                return redirect()->route('admin.dashboard');

            case Role::Manager:
                return redirect()->route('manager.index');

            case Role::Agency:
                return redirect()->route('agency.index');

            case Role::User:
                return redirect()->route('local.home');

            default:
                return redirect('/login');
        }
    }
}
