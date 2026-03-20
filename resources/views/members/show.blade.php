@extends('layouts.app')

@section('title', $member->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ url()->previous() }}" class="p-2 bg-gray-700 rounded-lg hover:bg-gray-600 transition flex-shrink-0">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-white truncate">{{ $member->name }}</h1>
                <p class="text-gray-400 text-sm">Member Profile</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($member->status === 'pending')
                <form action="{{ route('members.approve', $member) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition text-sm">Approve</button>
                </form>
                <form action="{{ route('members.reject', $member) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition text-sm">Reject</button>
                </form>
            @endif
            <a href="{{ route('members.download', $member) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition text-sm">Download Docs</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">{{ session('success') }}</div>
    @endif

    @php $profile = $member->memberProfile; @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Photo & Basic Info -->
        <div class="space-y-6">
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 text-center">
                @if($profile?->passport_photo)
                    <img src="{{ asset('storage/' . $profile->passport_photo) }}" class="w-32 h-32 rounded-xl object-cover mx-auto mb-4">
                @else
                    <div class="w-32 h-32 bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl text-gray-500">{{ substr($member->name, 0, 1) }}</span>
                    </div>
                @endif
                <h2 class="text-xl font-bold text-white">{{ $member->name }}</h2>
                @if($profile?->full_name_bangla)
                    <p class="text-gray-400">{{ $profile->full_name_bangla }}</p>
                @endif
                <div class="mt-4">
                    @if($member->status === 'approved')
                        <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm">Approved</span>
                    @elseif($member->status === 'pending')
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm">Pending</span>
                    @else
                        <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm">Rejected</span>
                    @endif
                </div>
            </div>

            <!-- Contact -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Contact</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-white">{{ $member->email }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-white">{{ $member->phone }}</span></div>
                    @if($profile?->phone_secondary)
                        <div class="flex justify-between"><span class="text-gray-400">Secondary</span><span class="text-white">{{ $profile->phone_secondary }}</span></div>
                    @endif
                </div>
            </div>

            <!-- Signature -->
            @if($profile?->signature_photo)
                <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Signature</h3>
                    <img src="{{ asset('storage/' . $profile->signature_photo) }}" class="max-h-20 bg-white p-2 rounded">
                </div>
            @endif
        </div>

        <!-- Middle Column -->
        <div class="space-y-6">
            <!-- Personal Info -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Personal Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Father's Name</span><span class="text-white">{{ $profile?->father_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Mother's Name</span><span class="text-white">{{ $profile?->mother_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Date of Birth</span><span class="text-white">{{ $profile?->date_of_birth?->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Gender</span><span class="text-white capitalize">{{ $profile?->gender }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Blood Group</span><span class="text-white">{{ $profile?->blood_group ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Occupation</span><span class="text-white">{{ $profile?->occupation ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Designation</span><span class="text-white">{{ $profile?->designation ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Organization</span><span class="text-white">{{ $profile?->organization ?? 'N/A' }}</span></div>
                </div>
            </div>

            <!-- NID -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">National ID</h3>
                <p class="text-white mb-4">{{ $profile?->nid_number }}</p>
                <div class="grid grid-cols-2 gap-4">
                    @if($profile?->nid_front_photo)
                        <div>
                            <p class="text-xs text-gray-400 mb-2">Front</p>
                            <a href="{{ asset('storage/' . $profile->nid_front_photo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $profile->nid_front_photo) }}" class="w-full rounded-lg">
                            </a>
                        </div>
                    @endif
                    @if($profile?->nid_back_photo)
                        <div>
                            <p class="text-xs text-gray-400 mb-2">Back</p>
                            <a href="{{ asset('storage/' . $profile->nid_back_photo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $profile->nid_back_photo) }}" class="w-full rounded-lg">
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Address -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Address</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-400 mb-1">Present Address</p>
                        <p class="text-white">{{ $profile?->present_address }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-1">Permanent Address</p>
                        <p class="text-white">{{ $profile?->permanent_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Nominee -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Nominee Information</h3>
                @if($profile?->nominee_photo)
                    <img src="{{ asset('storage/' . $profile->nominee_photo) }}" class="w-20 h-20 rounded-lg object-cover mb-4">
                @endif
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Name</span><span class="text-white">{{ $profile?->nominee_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Relation</span><span class="text-white">{{ $profile?->nominee_relation }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-white">{{ $profile?->nominee_phone }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">NID</span><span class="text-white">{{ $profile?->nominee_nid_number }}</span></div>
                    <div><p class="text-gray-400 mb-1">Address</p><p class="text-white text-xs">{{ $profile?->nominee_address }}</p></div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    @if($profile?->nominee_nid_front_photo)
                        <a href="{{ asset('storage/' . $profile->nominee_nid_front_photo) }}" target="_blank">
                            <img src="{{ asset('storage/' . $profile->nominee_nid_front_photo) }}" class="w-full rounded-lg">
                        </a>
                    @endif
                    @if($profile?->nominee_nid_back_photo)
                        <a href="{{ asset('storage/' . $profile->nominee_nid_back_photo) }}" target="_blank">
                            <img src="{{ asset('storage/' . $profile->nominee_nid_back_photo) }}" class="w-full rounded-lg">
                        </a>
                    @endif
                </div>
            </div>

            <!-- Banking -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Banking Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Bank</span><span class="text-white">{{ $profile?->bank_name ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Branch</span><span class="text-white">{{ $profile?->bank_branch ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Account Name</span><span class="text-white">{{ $profile?->bank_account_name ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Account No</span><span class="text-white">{{ $profile?->bank_account_number ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Routing</span><span class="text-white">{{ $profile?->bank_routing_number ?? 'N/A' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="text-white capitalize">{{ $profile?->account_type ?? 'N/A' }}</span></div>
                </div>
                @if($profile?->mobile_banking_provider)
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-gray-400 text-sm mb-2">Mobile Banking</p>
                        <p class="text-white">{{ $profile->mobile_banking_provider }}: {{ $profile->mobile_banking_number }}</p>
                    </div>
                @endif
            </div>

            <!-- Emergency Contact -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Emergency Contact</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Name</span><span class="text-white">{{ $profile?->emergency_contact_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Relation</span><span class="text-white">{{ $profile?->emergency_contact_relation }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-white">{{ $profile?->emergency_contact_phone }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
