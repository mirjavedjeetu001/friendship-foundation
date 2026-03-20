@extends('layouts.app')

@section('title', 'All Members')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">All Members</h1>
            <p class="text-gray-400 text-sm mt-1">Manage all registered members</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('members.pending') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm hover:bg-yellow-500 transition">Pending Approvals</a>
            <a href="{{ route('members.download-all') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-500 transition">Download All Data</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Member</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Role</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($members as $member)
                    <tr class="hover:bg-gray-700/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                @if($member->memberProfile?->passport_photo)
                                    <img src="{{ asset('storage/' . $member->memberProfile->passport_photo) }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-400 font-medium">{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-white font-medium">{{ $member->name }}</p>
                                    <p class="text-gray-500 text-xs">Joined {{ $member->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-300 text-sm">{{ $member->email }}</p>
                            <p class="text-gray-500 text-xs">{{ $member->phone }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($member->status === 'approved')
                                <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs">Approved</span>
                            @elseif($member->status === 'pending')
                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs">Pending</span>
                            @else
                                <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('members.update-role', $member) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="role" onchange="this.form.submit()" class="bg-gray-700 border border-gray-600 text-gray-300 text-xs rounded px-2 py-1">
                                    <option value="member" {{ $member->hasRole('member') ? 'selected' : '' }}>Member</option>
                                    <option value="accountant" {{ $member->hasRole('accountant') ? 'selected' : '' }}>Accountant</option>
                                    <option value="admin" {{ $member->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                                    <option value="super-admin" {{ $member->hasRole('super-admin') ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('members.show', $member) }}" class="p-1.5 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('members.download', $member) }}" class="p-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-500 transition" title="Download Documents">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No members found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $members->links() }}</div>
</div>
@endsection
