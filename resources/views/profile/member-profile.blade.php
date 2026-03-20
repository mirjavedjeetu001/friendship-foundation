@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: 'personal',
    sameAddress: {{ $profile && $profile->present_address === $profile->permanent_address ? 'true' : 'false' }}
}">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">My Profile</h1>
            <p class="text-gray-400 text-sm mt-1">Update your personal and banking information</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Header -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                @if($profile && $profile->passport_photo)
                    <img src="{{ $profile->passport_photo_url }}" alt="{{ $user->name }}" 
                        class="w-24 h-24 rounded-full object-cover border-4 border-indigo-500">
                @else
                    <div class="w-24 h-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold border-4 border-indigo-500">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <span class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 border-2 border-gray-800 rounded-full"></span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-gray-400">{{ $profile->full_name_bangla ?? '' }}</p>
                <p class="text-sm text-indigo-400 mt-1">{{ $user->email }}</p>
                <p class="text-sm text-gray-500">Member since {{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-700">
        <nav class="flex space-x-6">
            <button @click="activeTab = 'personal'" 
                :class="activeTab === 'personal' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                Personal Information
            </button>
            <button @click="activeTab = 'identity'" 
                :class="activeTab === 'identity' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                Identity Documents
            </button>
            <button @click="activeTab = 'nominee'" 
                :class="activeTab === 'nominee' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                Nominee Information
            </button>
            <button @click="activeTab = 'banking'" 
                :class="activeTab === 'banking' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-300'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                Banking Details
            </button>
        </nav>
    </div>

    <form method="POST" action="{{ route('profile.member.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Personal Information Tab -->
        <div x-show="activeTab === 'personal'" class="bg-gray-800 rounded-xl border border-gray-700 p-6 space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-gray-700 pb-3">Personal Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name (English) *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name (বাংলা)</label>
                    <input type="text" name="full_name_bangla" value="{{ old('full_name_bangla', $profile->full_name_bangla ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Father's Name *</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $profile->father_name ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Mother's Name *</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $profile->mother_name ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date of Birth *</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d') ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Gender *</label>
                    <select name="gender" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $profile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $profile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $profile->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Blood Group</label>
                    <select name="blood_group" class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Blood Group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                            <option value="{{ $group }}" {{ old('blood_group', $profile->blood_group ?? '') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Secondary Phone</label>
                    <input type="text" name="phone_secondary" value="{{ old('phone_secondary', $profile->phone_secondary ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation', $profile->occupation ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $profile->designation ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Organization</label>
                    <input type="text" name="organization" value="{{ old('organization', $profile->organization ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <h4 class="text-md font-semibold text-white border-b border-gray-700 pb-2 pt-4">Emergency Contact</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Contact Name *</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $profile->emergency_contact_name ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Contact Phone *</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Relationship *</label>
                    <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation', $profile->emergency_contact_relation ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <h4 class="text-md font-semibold text-white border-b border-gray-700 pb-2 pt-4">Address</h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Present Address *</label>
                    <textarea name="present_address" rows="2" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">{{ old('present_address', $profile->present_address ?? '') }}</textarea>
                </div>
                <label class="flex items-center gap-2 text-gray-300 text-sm">
                    <input type="checkbox" x-model="sameAddress" class="rounded border-gray-600 bg-gray-700 text-indigo-600">
                    <span>Permanent address same as present address</span>
                </label>
                <div x-show="!sameAddress">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Permanent Address *</label>
                    <textarea name="permanent_address" rows="2" :required="!sameAddress"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">{{ old('permanent_address', $profile->permanent_address ?? '') }}</textarea>
                </div>
                <input type="hidden" name="same_address" :value="sameAddress ? '1' : '0'">
            </div>
        </div>

        <!-- Identity Documents Tab -->
        <div x-show="activeTab === 'identity'" x-cloak class="bg-gray-800 rounded-xl border border-gray-700 p-6 space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-gray-700 pb-3">Identity Documents</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">NID Number *</label>
                    <input type="text" name="nid_number" value="{{ old('nid_number', $profile->nid_number ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Passport Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Passport Size Photo</label>
                    @if($profile && $profile->passport_photo)
                        <div class="mb-3">
                            <img src="{{ $profile->passport_photo_url }}" alt="Passport Photo" class="w-32 h-32 object-cover rounded-lg border border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="passport_photo" accept="image/*"
                        class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep current photo</p>
                </div>

                <!-- NID Front -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">NID Front</label>
                    @if($profile && $profile->nid_front_photo)
                        <div class="mb-3">
                            <img src="{{ $profile->nid_front_photo_url }}" alt="NID Front" class="w-full h-24 object-cover rounded-lg border border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="nid_front_photo" accept="image/*"
                        class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                </div>

                <!-- NID Back -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">NID Back</label>
                    @if($profile && $profile->nid_back_photo)
                        <div class="mb-3">
                            <img src="{{ $profile->nid_back_photo_url }}" alt="NID Back" class="w-full h-24 object-cover rounded-lg border border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="nid_back_photo" accept="image/*"
                        class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                </div>
            </div>

            <!-- Signature -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Signature</label>
                @if($profile && $profile->signature_photo)
                    <div class="mb-3">
                        <img src="{{ $profile->signature_photo_url }}" alt="Signature" class="h-16 object-contain rounded border border-gray-600 bg-white p-2">
                    </div>
                @endif
                <input type="file" name="signature_photo" accept="image/*"
                    class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
            </div>
        </div>

        <!-- Nominee Tab -->
        <div x-show="activeTab === 'nominee'" x-cloak class="bg-gray-800 rounded-xl border border-gray-700 p-6 space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-gray-700 pb-3">Nominee Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nominee Name *</label>
                    <input type="text" name="nominee_name" value="{{ old('nominee_name', $profile->nominee_name ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Relationship *</label>
                    <input type="text" name="nominee_relation" value="{{ old('nominee_relation', $profile->nominee_relation ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nominee Phone *</label>
                    <input type="text" name="nominee_phone" value="{{ old('nominee_phone', $profile->nominee_phone ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nominee NID *</label>
                    <input type="text" name="nominee_nid_number" value="{{ old('nominee_nid_number', $profile->nominee_nid_number ?? '') }}" required
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nominee Address *</label>
                <textarea name="nominee_address" rows="2" required
                    class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">{{ old('nominee_address', $profile->nominee_address ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Nominee Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nominee Photo</label>
                    @if($profile && $profile->nominee_photo)
                        <div class="mb-3">
                            <img src="{{ $profile->nominee_photo_url }}" alt="Nominee Photo" class="w-24 h-24 object-cover rounded-lg border border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="nominee_photo" accept="image/*"
                        class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                </div>

                <!-- Nominee NID Front -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nominee NID Front</label>
                    @if($profile && $profile->nominee_nid_front_photo)
                        <div class="mb-3">
                            <img src="{{ $profile->nominee_nid_front_photo_url }}" alt="Nominee NID Front" class="w-full h-20 object-cover rounded-lg border border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="nominee_nid_front_photo" accept="image/*"
                        class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                </div>

                <!-- Nominee NID Back -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nominee NID Back</label>
                    @if($profile && $profile->nominee_nid_back_photo)
                        <div class="mb-3">
                            <img src="{{ $profile->nominee_nid_back_photo_url }}" alt="Nominee NID Back" class="w-full h-20 object-cover rounded-lg border border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="nominee_nid_back_photo" accept="image/*"
                        class="w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                </div>
            </div>
        </div>

        <!-- Banking Tab -->
        <div x-show="activeTab === 'banking'" x-cloak class="bg-gray-800 rounded-xl border border-gray-700 p-6 space-y-6">
            <h3 class="text-lg font-semibold text-white border-b border-gray-700 pb-3">Banking Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Branch</label>
                    <input type="text" name="bank_branch" value="{{ old('bank_branch', $profile->bank_branch ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Account Holder Name</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $profile->bank_account_name ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Account Number</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $profile->bank_account_number ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Routing Number</label>
                    <input type="text" name="bank_routing_number" value="{{ old('bank_routing_number', $profile->bank_routing_number ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Account Type</label>
                    <select name="account_type" class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Type</option>
                        <option value="savings" {{ old('account_type', $profile->account_type ?? '') == 'savings' ? 'selected' : '' }}>Savings</option>
                        <option value="current" {{ old('account_type', $profile->account_type ?? '') == 'current' ? 'selected' : '' }}>Current</option>
                    </select>
                </div>
            </div>

            <h4 class="text-md font-semibold text-white border-b border-gray-700 pb-2 pt-4">Mobile Banking</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Provider</label>
                    <select name="mobile_banking_provider" class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Provider</option>
                        @foreach(['bKash', 'Nagad', 'Rocket', 'Upay', 'Other'] as $provider)
                            <option value="{{ $provider }}" {{ old('mobile_banking_provider', $profile->mobile_banking_provider ?? '') == $provider ? 'selected' : '' }}>{{ $provider }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Mobile Number</label>
                    <input type="text" name="mobile_banking_number" value="{{ old('mobile_banking_number', $profile->mobile_banking_number ?? '') }}"
                        class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-6">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
