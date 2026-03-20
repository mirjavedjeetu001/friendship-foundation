<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'], // 5MB max
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-removed');
    }

    /**
     * Delete the user's account.
     */
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

    /**
     * Display the member's full profile edit form.
     */
    public function memberEdit(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profile;

        return view('profile.member-profile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the member's full profile.
     */
    public function memberUpdate(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'full_name_bangla' => ['nullable', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:100'],
            'present_address' => ['required', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'same_address' => ['nullable', 'string'],
            'nid_number' => ['required', 'string', 'max:50'],
            'nominee_name' => ['required', 'string', 'max:255'],
            'nominee_relation' => ['required', 'string', 'max:100'],
            'nominee_phone' => ['required', 'string', 'max:20'],
            'nominee_nid_number' => ['required', 'string', 'max:50'],
            'nominee_address' => ['required', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_routing_number' => ['nullable', 'string', 'max:50'],
            'account_type' => ['nullable', 'in:savings,current'],
            'mobile_banking_provider' => ['nullable', 'string', 'max:50'],
            'mobile_banking_number' => ['nullable', 'string', 'max:20'],
            'passport_photo' => ['nullable', 'image', 'max:5120'],
            'nid_front_photo' => ['nullable', 'image', 'max:5120'],
            'nid_back_photo' => ['nullable', 'image', 'max:5120'],
            'signature_photo' => ['nullable', 'image', 'max:5120'],
            'nominee_photo' => ['nullable', 'image', 'max:5120'],
            'nominee_nid_front_photo' => ['nullable', 'image', 'max:5120'],
            'nominee_nid_back_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        // Update user
        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->save();

        // Get or create profile
        $profile = $user->profile;
        if (!$profile) {
            $profile = new \App\Models\MemberProfile();
            $profile->user_id = $user->id;
        }

        // Handle same address checkbox
        if ($request->input('same_address') === '1') {
            $validated['permanent_address'] = $validated['present_address'];
        }

        // Update profile fields
        $profileFields = [
            'full_name_bangla', 'father_name', 'mother_name', 'date_of_birth',
            'gender', 'blood_group', 'phone_secondary', 'occupation',
            'designation', 'organization', 'emergency_contact_name',
            'emergency_contact_phone', 'emergency_contact_relation',
            'present_address', 'permanent_address', 'nid_number',
            'nominee_name', 'nominee_relation', 'nominee_phone',
            'nominee_nid_number', 'nominee_address', 'bank_name',
            'bank_branch', 'bank_account_name', 'bank_account_number',
            'bank_routing_number', 'account_type', 'mobile_banking_provider',
            'mobile_banking_number',
        ];

        foreach ($profileFields as $field) {
            if (isset($validated[$field])) {
                $profile->$field = $validated[$field];
            }
        }

        // Handle file uploads
        $fileFields = [
            'passport_photo' => 'member-photos',
            'nid_front_photo' => 'member-nids',
            'nid_back_photo' => 'member-nids',
            'signature_photo' => 'member-signatures',
            'nominee_photo' => 'nominee-photos',
            'nominee_nid_front_photo' => 'nominee-nids',
            'nominee_nid_back_photo' => 'nominee-nids',
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                // Delete old file
                if ($profile->$field) {
                    Storage::disk('public')->delete($profile->$field);
                }
                // Store new file
                $profile->$field = $request->file($field)->store($folder, 'public');
            }
        }

        $profile->save();

        return Redirect::route('profile.member.edit')->with('success', 'Profile updated successfully!');
    }
}
