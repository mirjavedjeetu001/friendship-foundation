<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Election & Poll Management
            </h2>
            <a href="{{ route('admin.elections.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
                + New Election/Poll
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Tabs -->
                    <div class="flex space-x-4 mb-6 border-b border-gray-700 pb-4">
                        <a href="{{ route('admin.elections.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                            All Elections
                        </a>
                        <a href="{{ route('admin.elections.history') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm">
                            History
                        </a>
                    </div>

                    @if($elections->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-300">No Elections</h3>
                            <p class="mt-1 text-gray-500">Create a new election or poll</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Votes</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($elections as $election)
                                        <tr class="hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-white">{{ $election->title_bn ?? $election->title }}</div>
                                                <div class="text-xs text-gray-500">{{ $election->positions->count() }} positions/options</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $election->type === 'election' ? 'bg-purple-900 text-purple-300' : 'bg-blue-900 text-blue-300' }}">
                                                    {{ $election->type === 'election' ? 'Committee Election' : 'Poll' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                <div>Start: {{ $election->start_time->format('d M Y, h:i A') }}</div>
                                                <div>End: {{ $election->end_time->format('d M Y, h:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClass = match($election->status) {
                                                        'draft' => 'bg-gray-700 text-gray-300',
                                                        'upcoming' => 'bg-yellow-900 text-yellow-300',
                                                        'active' => 'bg-green-900 text-green-300',
                                                        'paused' => 'bg-orange-900 text-orange-300',
                                                        'completed' => 'bg-blue-900 text-blue-300',
                                                        'cancelled' => 'bg-red-900 text-red-300',
                                                        default => 'bg-gray-700 text-gray-300',
                                                    };
                                                    $statusText = match($election->status) {
                                                        'draft' => 'Draft',
                                                        'upcoming' => 'Upcoming',
                                                        'active' => 'Active',
                                                        'paused' => 'Paused',
                                                        'completed' => 'Completed',
                                                        'cancelled' => 'Cancelled',
                                                        default => $election->status,
                                                    };
                                                @endphp
                                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $election->total_votes }} votes
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end items-center space-x-2">
                                                    <a href="{{ route('admin.elections.show', $election) }}" class="text-indigo-400 hover:text-indigo-300" title="View Details">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                    
                                                    @if(in_array($election->status, ['upcoming', 'draft']))
                                                        <a href="{{ route('admin.elections.edit', $election) }}" class="text-yellow-400 hover:text-yellow-300" title="Edit">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('admin.elections.start', $election) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-400 hover:text-green-300" onclick="return confirm('Start the election now?')" title="Start Now">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($election->status === 'active')
                                                        <form action="{{ route('admin.elections.stop', $election) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-orange-400 hover:text-orange-300" onclick="return confirm('Pause the election? You can edit and resume later.')" title="Pause Election">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.elections.end', $election) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('End the election and declare results?')" title="End & Declare Results">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($election->status === 'paused')
                                                        <a href="{{ route('admin.elections.edit', $election) }}" class="text-yellow-400 hover:text-yellow-300" title="Edit">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('admin.elections.resume', $election) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-400 hover:text-green-300" onclick="return confirm('Resume the election?')" title="Resume">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($election->status === 'completed')
                                                        <a href="{{ route('admin.elections.results', $election) }}" class="text-blue-400 hover:text-blue-300" title="View Results">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('admin.elections.destroy', $election) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('⚠️ WARNING: This will PERMANENTLY DELETE:\n\n• All positions\n• All candidates\n• All votes\n• Committee members\n• Past election history\n\nThis action CANNOT be undone!\n\nAre you absolutely sure?')" title="Delete Election">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if(!in_array($election->status, ['completed', 'active']))
                                                        <form action="{{ route('admin.elections.destroy', $election) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Delete this election permanently? This cannot be undone!')" title="Delete">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $elections->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
