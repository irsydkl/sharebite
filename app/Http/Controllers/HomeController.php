<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the application landing page or redirect to role-specific dashboard.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRouteName());
        }

        return view('Pages.User.home');
    }
}
