<?php

namespace App\Http\Controllers;

use App\Http\Requests\DonorProfileUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user->isDonatur()) {
            $user->load('donorProfile');
        }

        return view('profile.edit', [
            'user' => $user,
            'donorProfile' => $user->donorProfile,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateStore(DonorProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $profile = $user->donorProfile;

        $requiresReapproval = false;

        DB::transaction(function () use ($validated, $user, $profile, &$requiresReapproval): void {
            $criticalChanged = $profile->store_name !== $validated['store_name']
                || $profile->store_address !== $validated['store_address']
                || (float) $profile->latitude !== (float) $validated['store_latitude']
                || (float) $profile->longitude !== (float) $validated['store_longitude'];

            $profile->fill([
                'store_name' => $validated['store_name'],
                'store_description' => $validated['store_description'] ?? null,
                'store_address' => $validated['store_address'],
                'latitude' => $validated['store_latitude'],
                'longitude' => $validated['store_longitude'],
            ]);

            if ($criticalChanged && $profile->approval_status === 'approved') {
                $profile->approval_status = 'pending';
                $profile->is_verified = false;
                $profile->location_verified = false;
                $profile->verified_by = null;
                $profile->verified_at = null;
                $requiresReapproval = true;
            }

            $profile->save();

            $user->update([
                'latitude' => $validated['store_latitude'],
                'longitude' => $validated['store_longitude'],
            ]);
        });

        $status = $requiresReapproval ? 'store-updated-pending' : 'store-updated';

        return Redirect::route('profile.edit')->with('status', $status);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
