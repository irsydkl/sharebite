<?php

namespace App\Http\Controllers;

use App\Models\Food;
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

        $foods = Food::where('status', 'available')
            ->where('approval_status', 'approved')
            ->with(['category', 'images', 'donor.donorProfile'])
            ->latest()
            ->get();

        return view('Pages.User.home', compact('foods'));
    }
}
