<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ $appSettings->app_name ?? 'Allied Group' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-900">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                @if($appSettings->logo)
                    <img src="{{ $appSettings->logo_url }}" alt="{{ $appSettings->app_name ?? 'Allied Group' }}" class="w-14 h-14 rounded-xl object-contain mx-auto mb-4">
                @else
                    <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                @endif
                <h1 class="text-2xl font-bold text-white">{{ $appSettings->app_name ?? 'Allied Group' }}</h1>
                <p class="text-gray-400 text-sm mt-1">Member Registration</p>
            </div>

            <!-- Registration Card -->
            @php
                $initialStep = 1;
                if ($errors->any()) {
                    $errorKeys = $errors->keys();
                    // Step 5: Account fields
                    $step5Fields = ['email', 'password', 'password_confirmation'];
                    // Step 4: Banking fields
                    $step4Fields = ['bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number', 'bank_routing_number', 'account_type', 'mobile_banking_provider', 'mobile_banking_number'];
                    // Step 3: Nominee fields
                    $step3Fields = ['nominee_name', 'nominee_relation', 'nominee_phone', 'nominee_nid_number', 'nominee_photo', 'nominee_nid_front_photo', 'nominee_nid_back_photo', 'nominee_address'];
                    // Step 2: Identity fields
                    $step2Fields = ['nid_number', 'nid_front_photo', 'nid_back_photo', 'passport_photo', 'signature_photo'];
                    
                    foreach ($errorKeys as $key) {
                        if (in_array($key, $step5Fields)) { $initialStep = 5; break; }
                        if (in_array($key, $step4Fields)) { $initialStep = 4; break; }
                        if (in_array($key, $step3Fields)) { $initialStep = 3; break; }
                        if (in_array($key, $step2Fields)) { $initialStep = 2; break; }
                        // Default is step 1 (Personal fields)
                    }
                }
            @endphp
            <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 p-6 sm:p-8" x-data="{ step: {{ $initialStep }}, sameAddress: false }">
                <!-- Progress Steps -->
                <div class="flex justify-between mb-8 relative">
                    <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-700">
                        <div class="h-full bg-indigo-600 transition-all duration-300" :style="'width: ' + ((step - 1) * 25) + '%'"></div>
                    </div>
                    <template x-for="(label, index) in ['Personal', 'Identity', 'Nominee', 'Banking', 'Account']" :key="index">
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all"
                                :class="step > index + 1 ? 'bg-indigo-600 text-white' : (step === index + 1 ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-400')">
                                <span x-text="index + 1"></span>
                            </div>
                            <span class="text-xs mt-2 text-gray-400 hidden sm:block" x-text="label"></span>
                        </div>
                    </template>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registrationForm">
                    @csrf

                    <!-- Step 1: Personal Information -->
                    <div x-show="step === 1" x-transition>
                        <h3 class="text-lg font-semibold text-white mb-6">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Full Name (English) *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Full Name (বাংলা)</label>
                                <input type="text" name="full_name_bangla" value="{{ old('full_name_bangla') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Father's Name *</label>
                                <input type="text" name="father_name" value="{{ old('father_name') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Mother's Name *</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Date of Birth *</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Gender *</label>
                                <select name="gender" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Blood Group</label>
                                <select name="blood_group" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Phone Number *</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="01XXXXXXXXX">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Secondary Phone</label>
                                <input type="tel" name="phone_secondary" value="{{ old('phone_secondary') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Occupation</label>
                                <input type="text" name="occupation" value="{{ old('occupation') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Designation</label>
                                <input type="text" name="designation" value="{{ old('designation') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Organization/Company</label>
                                <input type="text" name="organization" value="{{ old('organization') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Present Address *</label>
                                <textarea name="present_address" x-ref="presentAddress" @input="if(sameAddress) { $refs.permanentAddress.value = $el.value }" rows="2" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white resize-none">{{ old('present_address') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="flex items-center text-sm text-gray-400 mb-2">
                                    <input type="checkbox" x-model="sameAddress" @change="if(sameAddress) { $refs.permanentAddress.value = $refs.presentAddress.value }" class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-indigo-600 focus:ring-indigo-500 mr-2">
                                    Same as present address
                                </label>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Permanent Address *</label>
                                <textarea name="permanent_address" x-ref="permanentAddress" rows="2" required :readonly="sameAddress" :class="sameAddress ? 'opacity-50 cursor-not-allowed' : ''" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white resize-none">{{ old('permanent_address') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Emergency Contact Name *</label>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Emergency Contact Phone *</label>
                                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Relation with Emergency Contact *</label>
                                <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}" required placeholder="e.g. Father, Brother, Wife" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Identity Documents -->
                    <div x-show="step === 2" x-transition x-cloak>
                        <h3 class="text-lg font-semibold text-white mb-6">Identity Documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">NID Number *</label>
                                <input type="text" name="nid_number" value="{{ old('nid_number') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="Enter your National ID number">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Passport Size Photo *</label>
                                <input type="file" name="passport_photo" accept="image/*" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                                <p class="text-xs text-gray-500 mt-1">Recent passport size photo (max 2MB)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Signature Photo</label>
                                <input type="file" name="signature_photo" accept="image/*" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                                <p class="text-xs text-gray-500 mt-1">Signature on white paper (max 1MB)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">NID Front Side *</label>
                                <input type="file" name="nid_front_photo" accept="image/*" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">NID Back Side *</label>
                                <input type="file" name="nid_back_photo" accept="image/*" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Nominee Information -->
                    <div x-show="step === 3" x-transition x-cloak>
                        <h3 class="text-lg font-semibold text-white mb-6">Nominee Information</h3>
                        <p class="text-sm text-gray-400 mb-6">Nominee will receive benefits in case of any emergency.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee Full Name *</label>
                                <input type="text" name="nominee_name" value="{{ old('nominee_name') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Relation with Nominee *</label>
                                <input type="text" name="nominee_relation" value="{{ old('nominee_relation') }}" required placeholder="e.g. Wife, Son, Daughter" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee Phone *</label>
                                <input type="tel" name="nominee_phone" value="{{ old('nominee_phone') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee NID Number *</label>
                                <input type="text" name="nominee_nid_number" value="{{ old('nominee_nid_number') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee Address *</label>
                                <textarea name="nominee_address" rows="2" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white resize-none">{{ old('nominee_address') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee Photo *</label>
                                <input type="file" name="nominee_photo" accept="image/*" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee NID Front *</label>
                                <input type="file" name="nominee_nid_front_photo" accept="image/*" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Nominee NID Back *</label>
                                <input type="file" name="nominee_nid_back_photo" accept="image/*" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Banking Information -->
                    <div x-show="step === 4" x-transition x-cloak>
                        <h3 class="text-lg font-semibold text-white mb-6">Banking Information</h3>
                        <p class="text-sm text-gray-400 mb-6">Optional but recommended for withdrawals and refunds.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Bank Name</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="e.g. Dutch Bangla Bank">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Branch Name</label>
                                <input type="text" name="bank_branch" value="{{ old('bank_branch') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Account Holder Name</label>
                                <input type="text" name="bank_account_name" value="{{ old('bank_account_name') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Account Number</label>
                                <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Routing Number</label>
                                <input type="text" name="bank_routing_number" value="{{ old('bank_routing_number') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Account Type</label>
                                <select name="account_type" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                                    <option value="">Select Type</option>
                                    <option value="savings" {{ old('account_type') == 'savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="current" {{ old('account_type') == 'current' ? 'selected' : '' }}>Current</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 border-t border-gray-700 pt-4 mt-2">
                                <h4 class="text-sm font-medium text-gray-300 mb-4">Mobile Banking (Optional)</h4>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Mobile Banking Provider</label>
                                <select name="mobile_banking_provider" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white">
                                    <option value="">Select Provider</option>
                                    <option value="bKash" {{ old('mobile_banking_provider') == 'bKash' ? 'selected' : '' }}>bKash</option>
                                    <option value="Nagad" {{ old('mobile_banking_provider') == 'Nagad' ? 'selected' : '' }}>Nagad</option>
                                    <option value="Rocket" {{ old('mobile_banking_provider') == 'Rocket' ? 'selected' : '' }}>Rocket</option>
                                    <option value="Upay" {{ old('mobile_banking_provider') == 'Upay' ? 'selected' : '' }}>Upay</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Mobile Banking Number</label>
                                <input type="tel" name="mobile_banking_number" value="{{ old('mobile_banking_number') }}" class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="01XXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Account Setup -->
                    <div x-show="step === 5" x-transition x-cloak>
                        <h3 class="text-lg font-semibold text-white mb-6">Account Setup</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="your@email.com">
                                <p class="text-xs text-gray-500 mt-1">You'll receive approval notification on this email</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Password *</label>
                                <input type="password" name="password" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="••••••••">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-white" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="mt-6 p-4 bg-gray-700/50 rounded-lg">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" required class="w-4 h-4 mt-0.5 rounded border-gray-600 bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-400">I agree to the terms and conditions. I confirm that all information provided is accurate and documents are genuine.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-700">
                        <button type="button" x-show="step > 1" @click="step--" class="px-6 py-2.5 text-gray-400 hover:text-white transition">← Previous</button>
                        <div x-show="step === 1"></div>
                        <button type="button" x-show="step < 5" @click="step++" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-500 transition">Next →</button>
                        <button type="submit" x-show="step === 5" class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-500 transition">Submit Registration</button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-6">
                <p class="text-gray-400 text-sm">Already have an account? <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300">Sign in</a></p>
            </div>
            <div class="text-center mt-4">
                <p class="text-gray-500 text-xs">Developed by <span class="font-medium text-gray-400">Mir Javed Jeetu</span> | <a href="tel:01811480222" class="text-gray-400 hover:text-indigo-400">01811480222</a></p>
            </div>
        </div>
    </div>
    <script>
        // File size validation
        const maxFileSize = 5 * 1024 * 1024; // 5MB per file
        const maxTotalSize = 20 * 1024 * 1024; // 20MB total

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Copy present address to permanent if sameAddress is checked
            const el = document.querySelector('[x-data]');
            if (el && Alpine.$data(el).sameAddress) {
                document.querySelector('[name="permanent_address"]').value = document.querySelector('[name="present_address"]').value;
            }

            const fileInputs = this.querySelectorAll('input[type="file"]');
            let totalSize = 0;
            let hasLargeFile = false;
            let largeFileName = '';

            fileInputs.forEach(input => {
                if (input.files.length > 0) {
                    const file = input.files[0];
                    totalSize += file.size;
                    if (file.size > maxFileSize) {
                        hasLargeFile = true;
                        largeFileName = file.name;
                    }
                }
            });

            if (hasLargeFile) {
                e.preventDefault();
                alert('File "' + largeFileName + '" is too large. Maximum size per file is 2MB. Please compress or resize your images.');
                return false;
            }

            if (totalSize > maxTotalSize) {
                e.preventDefault();
                alert('Total file size exceeds 20MB. Please compress or resize your images before uploading.');
                return false;
            }
        });

        // Individual file validation on change
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    if (file.size > maxFileSize) {
                        alert('File "' + file.name + '" is too large (' + (file.size / 1024 / 1024).toFixed(2) + 'MB). Maximum size is 2MB. Please choose a smaller file.');
                        this.value = '';
                    }
                }
            });
        });
    </script>
</body>
</html>
