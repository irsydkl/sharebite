<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterDonaturRequest;
use App\Models\DonorProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisterDonaturController extends Controller
{
    public function create(): View
    {
        return view('auth.register-donatur');
    }

    public function store(RegisterDonaturRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'donatur',
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'latitude' => $validated['store_latitude'],
                'longitude' => $validated['store_longitude'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            DonorProfile::create([
                'user_id' => $user->id,
                'store_name' => $validated['store_name'],
                'store_description' => $validated['store_description'] ?? null,
                'store_address' => $validated['store_address'],
                'latitude' => $validated['store_latitude'],
                'longitude' => $validated['store_longitude'],
                'approval_status' => 'pending',
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route($user->dashboardRouteName());
    }
}
