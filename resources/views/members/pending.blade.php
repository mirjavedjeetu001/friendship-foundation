@extends('layouts.app')

@section('title', 'Pending Members')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Pending Approvals</h1>
            <p class="text-gray-400 text-sm mt-1">Review and approve new member registrations</p>
        </div>
        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm font-medium">{{ $members->total() }} Pending</span>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        @forelse($members as $member)
            <div class="p-4 border-b border-gray-700 hover:bg-gray-700/50 transition">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-4">
                        @if($member->memberProfile?->passport_photo)
                            <img src="{{ asset('storage/' . $member->memberProfile->passport_photo) }}" class="w-16 h-16 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-16 bg-gray-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-white font-medium">{{ $member->name }}</h3>
                            <p class="text-gray-400 text-sm">{{ $member->email }}</p>
                            <p class="text-gray-500 text-xs mt-1">Applied: {{ $member->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('members.show', $member) }}" class="px-3 py-1.5 bg-gray-700 text-gray-300 rounded-lg text-sm hover:bg-gray-600 transition">View Details</a>
                        <form action="{{ route('members.approve', $member) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm hover:bg-green-500 transition">Approve</button>
                        </form>
                        <form action="{{ route('members.reject', $member) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this application?')">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm hover:bg-red-500 transition">Reject</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p>No pending approvals</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $members->links() }}</div>
</div>
@endsection
