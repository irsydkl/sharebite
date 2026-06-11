<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Redirect or render history based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isDonatur()) {
            return redirect()->route('donatur.claims');
        }

        if ($user->isUser()) {
            return view('Pages.User.riwayat', [
                'claims' => $user->claims()->with(['food', 'food.donor', 'payment', 'rating'])->latest()->get(),
            ]);
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.payouts');
        }

        return redirect()->route('home');
    }
}
