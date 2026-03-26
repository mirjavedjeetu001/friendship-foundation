<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                {{ $election->title_bn ?? $election->title }}
            </h2>
            <a href="{{ route('admin.elections.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Status Card -->
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center space-x-4">
                        @php
                            $statusClass = match($election->status) {
                                'draft' => 'bg-gray-700 text-gray-300',
                                'upcoming' => 'bg-yellow-900 text-yellow-300',
                                'active' => 'bg-green-900 text-green-300 animate-pulse',
                                'completed' => 'bg-blue-900 text-blue-300',
                                'cancelled' => 'bg-red-900 text-red-300',
                                default => 'bg-gray-700 text-gray-300',
                            };
                            $statusText = match($election->status) {
                                'draft' => 'Draft',
                                'upcoming' => 'Upcoming',
                                'active' => '🔴 Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                default => $election->status,
                            };
                        @endphp
                        <span class="px-4 py-2 rounded-full text-lg font-semibold {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm {{ $election->type === 'election' ? 'bg-purple-900 text-purple-300' : 'bg-blue-900 text-blue-300' }}">
                            {{ $election->type === 'election' ? 'Committee Election' : 'Poll' }}
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        @if($election->status === 'upcoming')
                            <a href="{{ route('admin.elections.edit', $election) }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                Edit
                            </a>
                            <form action="{{ route('admin.elections.start', $election) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm" onclick="return confirm('Start voting now?')">
                                    Start Now
                                </button>
                            </form>
                        @endif
                        @if($election->status === 'active')
                            <form action="{{ route('admin.elections.end', $election) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" onclick="return confirm('End voting now?')">
                                    Stop Voting
                                </button>
                            </form>
                        @endif
                        @if(!in_array($election->status, ['completed', 'cancelled']))
                            <form action="{{ route('admin.elections.cancel', $election) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-red-400 rounded-lg text-sm" onclick="return confirm('Cancel this election?')">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="text-gray-400 text-sm mb-1">Start Time</div>
                    <div class="text-white">{{ $election->start_time->format('d M Y, h:i A') }}</div>
                </div>
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="text-gray-400 text-sm mb-1">End Time</div>
                    <div class="text-white">{{ $election->end_time->format('d M Y, h:i A') }}</div>
                </div>
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="text-gray-400 text-sm mb-1">Total Votes</div>
                    <div class="text-2xl font-bold text-indigo-400">{{ $election->total_votes }}</div>
                </div>
            </div>

            @if($election->description)
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-2">Description</h3>
                    <p class="text-gray-300">{{ $election->description_bn ?? $election->description }}</p>
                </div>
            @endif

            <!-- Positions & Candidates (for election) -->
            @if($election->type === 'election')
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Positions & Candidates</h3>
                    <div class="space-y-6">
                        @foreach($election->positions as $position)
                            <div class="border border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-lg font-medium text-indigo-400">
                                        {{ $position->name_bn ?? $position->name }}
                                    </h4>
                                    <span class="text-sm text-gray-500">{{ $position->candidates->count() }} candidates</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($position->candidates as $candidate)
                                        <div class="flex items-center space-x-3 p-3 bg-gray-700/50 rounded-lg {{ $candidate->is_winner ? 'ring-2 ring-green-500' : '' }}">
                                            @if($candidate->user->profile && $candidate->user->profile->passport_photo)
                                                <img src="{{ asset('storage/' . $candidate->user->profile->passport_photo) }}" 
                                                    class="w-12 h-12 rounded-full object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center">
                                                    <span class="text-lg text-gray-400">{{ substr($candidate->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <div class="text-white font-medium">{{ $candidate->user->name }}</div>
                                                <div class="text-sm text-gray-400">{{ $candidate->votes_count }} votes ({{ $candidate->vote_percentage }}%)</div>
                                            </div>
                                            @if($candidate->is_winner)
                                                <span class="text-green-400 text-xl">🏆</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Poll Options -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Options & Votes</h3>
                    <div class="space-y-4">
                        @foreach($election->pollOptions as $option)
                            <div class="relative">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-white">{{ $option->option_text_bn ?? $option->option_text }}</span>
                                    <span class="text-gray-400">{{ $option->votes_count }} votes ({{ $option->vote_percentage }}%)</span>
                                </div>
                                <div class="h-4 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full transition-all" 
                                        style="width: {{ $option->vote_percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
