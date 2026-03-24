<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationPendingMail;
use App\Models\User;
use App\Models\MemberProfile;
use App\Models\MonthlySetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $appSettings = MonthlySetting::getSettings();
        return view('auth.register', compact('appSettings'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // User basic info
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Personal Information
            'full_name_bangla' => ['nullable', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            
            // Contact
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relation' => ['required', 'string', 'max:100'],
            
            // Address
            'present_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['required', 'string', 'max:500'],
            
            // NID
            'nid_number' => ['required', 'string', 'max:50'],
            'nid_front_photo' => ['required', 'image', 'max:5120'],
            'nid_back_photo' => ['required', 'image', 'max:5120'],
            
            // Passport Photo
            'passport_photo' => ['required', 'image', 'max:5120'],
            
            // Nominee
            'nominee_name' => ['required', 'string', 'max:255'],
            'nominee_relation' => ['required', 'string', 'max:100'],
            'nominee_phone' => ['required', 'string', 'max:20'],
            'nominee_nid_number' => ['required', 'string', 'max:50'],
            'nominee_photo' => ['required', 'image', 'max:5120'],
            'nominee_nid_front_photo' => ['required', 'image', 'max:5120'],
            'nominee_nid_back_photo' => ['required', 'image', 'max:5120'],
            'nominee_address' => ['required', 'string', 'max:500'],
            
            // Banking
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_routing_number' => ['nullable', 'string', 'max:20'],
            'account_type' => ['nullable', 'in:savings,current'],
            'mobile_banking_provider' => ['nullable', 'string', 'max:50'],
            'mobile_banking_number' => ['nullable', 'string', 'max:20'],
            
            // Signature
            'signature_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        // Use database transaction to ensure both user and profile are created together
        $user = DB::transaction(function () use ($request) {
            // Create user with pending status
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => User::STATUS_PENDING,
                'is_active' => false,
                'joined_date' => now(),
            ]);

            // Assign member role
            $user->assignRole('member');

            // Handle file uploads
            $profileData = $request->only([
                'full_name_bangla', 'father_name', 'mother_name', 'date_of_birth',
                'gender', 'blood_group', 'occupation', 'designation', 'organization',
                'phone_secondary', 'emergency_contact_name', 'emergency_contact_phone',
                'emergency_contact_relation', 'present_address', 'permanent_address',
                'nid_number', 'nominee_name', 'nominee_relation', 'nominee_phone',
                'nominee_nid_number', 'nominee_address', 'bank_name', 'bank_branch',
                'bank_account_name', 'bank_account_number', 'bank_routing_number',
                'account_type', 'mobile_banking_provider', 'mobile_banking_number',
            ]);

            // Upload photos
            $photoFields = [
                'passport_photo', 'nid_front_photo', 'nid_back_photo',
                'nominee_photo', 'nominee_nid_front_photo', 'nominee_nid_back_photo',
                'signature_photo'
            ];

            foreach ($photoFields as $field) {
                if ($request->hasFile($field)) {
                    $profileData[$field] = $request->file($field)->store('member-documents/' . $user->id, 'public');
                }
            }

            // Create member profile
            $user->memberProfile()->create($profileData);

            return $user;
        });

        event(new Registered($user));

        // Send registration pending email
        try {
            Mail::to($user->email)->send(new RegistrationPendingMail($user));
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        // Don't auto-login - redirect to pending page
        return redirect()->route('registration.pending');
    }

    /**
     * Show registration pending page
     */
    public function pending(): View
    {
        $appSettings = MonthlySetting::getSettings();
        return view('auth.registration-pending', compact('appSettings'));
    }
}

