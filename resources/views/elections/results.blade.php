<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Results: {{ $election->title_bn ?? $election->title }}
            </h2>
            <a href="{{ route('elections.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="liveResults()" x-init="startPolling()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Banner -->
            @if($election->isActive())
                <div class="bg-gradient-to-r from-green-900 to-emerald-900 rounded-lg p-4 mb-6">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-3"></span>
                            <span class="text-white font-medium">Live Results - Updates every 5 seconds</span>
                        </div>
                        <div class="text-green-200" x-data="{ 
                            remaining: {{ now()->diffInSeconds($election->end_time) }},
                            init() {
                                setInterval(() => {
                                    if (this.remaining > 0) this.remaining--;
                                    else window.location.reload();
                                }, 1000);
                            }
                        }">
                            Ends in: <span x-text="Math.floor(remaining / 3600) + ':' + Math.floor((remaining % 3600) / 60).toString().padStart(2,'0') + ':' + (remaining % 60).toString().padStart(2,'0')"></span>
                        </div>
                    </div>
                </div>
            @elseif($election->hasEnded())
                <div class="bg-gradient-to-r from-indigo-900 to-purple-900 rounded-lg p-6 mb-6 text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">🏆 Election Completed</h3>
                    <p class="text-indigo-200">{{ $election->end_time->format('d M Y, h:i A') }}</p>
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-indigo-400" x-text="totalVotes">{{ $totalVoters }}</div>
                    <div class="text-gray-400 text-sm">Total Votes</div>
                </div>
                @if($election->type === 'election')
                    <div class="bg-gray-800 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-purple-400">{{ $election->positions->count() }}</div>
                        <div class="text-gray-400 text-sm">Positions</div>
                    </div>
                @endif
                <div class="bg-gray-800 rounded-lg p-4 text-center">
                    @if($hasVoted)
                        <div class="text-3xl">✓</div>
                        <div class="text-green-400 text-sm">You voted</div>
                    @else
                        <div class="text-3xl">✗</div>
                        <div class="text-yellow-400 text-sm">Not voted</div>
                    @endif
                </div>
                @if($election->isActive() && !$hasVoted)
                    <a href="{{ route('elections.vote', $election) }}" class="bg-green-700 hover:bg-green-600 rounded-lg p-4 text-center">
                        <div class="text-2xl">🗳️</div>
                        <div class="text-white text-sm font-medium">Vote Now</div>
                    </a>
                @endif
            </div>

            @if($election->type === 'election')
                <!-- Election Results -->
                <div class="space-y-6">
                    @foreach($election->positions as $position)
                        <div class="bg-gray-800 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-indigo-400 mb-4">
                                {{ $position->name_bn ?? $position->name }}
                            </h3>
                            <div class="space-y-4">
                                @foreach($position->candidates->sortByDesc('votes_count') as $index => $candidate)
                                    <div class="relative" x-data="{ votes: {{ $candidate->votes_count }}, percentage: {{ $candidate->vote_percentage }} }">
                                        <div class="flex items-center space-x-4 mb-2">
                                            <div class="flex-shrink-0 relative">
                                                @if($candidate->user->profile && $candidate->user->profile->passport_photo)
                                                    <img src="{{ asset('storage/' . $candidate->user->profile->passport_photo) }}" 
                                                        class="w-12 h-12 rounded-full object-cover {{ $candidate->is_winner ? 'ring-2 ring-yellow-500' : '' }}">
                                                @else
                                                    <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center {{ $candidate->is_winner ? 'ring-2 ring-yellow-500' : '' }}">
                                                        <span class="text-lg text-gray-400">{{ substr($candidate->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                @if($candidate->is_winner)
                                                    <span class="absolute -top-1 -right-1 text-lg">🏆</span>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-white font-medium {{ $candidate->is_winner ? 'text-yellow-400' : '' }}">
                                                        {{ $candidate->user->name }}
                                                    </span>
                                                    <span class="text-gray-400" id="votes-{{ $candidate->id }}">
                                                        {{ $candidate->votes_count }} votes (<span id="percentage-{{ $candidate->id }}">{{ $candidate->vote_percentage }}</span>%)
                                                    </span>
                                                </div>
                                                <div class="h-3 bg-gray-700 rounded-full overflow-hidden mt-1">
                                                    <div class="h-full {{ $candidate->is_winner ? 'bg-yellow-500' : ($index === 0 ? 'bg-indigo-500' : 'bg-indigo-700') }} rounded-full transition-all duration-500" 
                                                        style="width: {{ $candidate->vote_percentage }}%"
                                                        id="bar-{{ $candidate->id }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Poll Results -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="space-y-4">
                        @foreach($election->pollOptions->sortByDesc('votes_count') as $index => $option)
                            <div class="p-4 {{ $index === 0 && $election->hasEnded() ? 'bg-indigo-900/30 border border-indigo-700' : 'bg-gray-700/30' }} rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-white font-medium">
                                        @if($index === 0 && $election->hasEnded())
                                            🏆
                                        @endif
                                        {{ $option->option_text_bn ?? $option->option_text }}
                                    </span>
                                    <span class="text-gray-400" id="poll-votes-{{ $option->id }}">
                                        {{ $option->votes_count }} votes (<span id="poll-percentage-{{ $option->id }}">{{ $option->vote_percentage }}</span>%)
                                    </span>
                                </div>
                                <div class="h-4 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $index === 0 && $election->hasEnded() ? 'bg-yellow-500' : 'bg-indigo-600' }} rounded-full transition-all duration-500" 
                                        style="width: {{ $option->vote_percentage }}%"
                                        id="poll-bar-{{ $option->id }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($election->isActive())
        @push('scripts')
        <script>
            function liveResults() {
                return {
                    totalVotes: {{ $totalVoters }},
                    polling: null,

                    startPolling() {
                        this.polling = setInterval(() => {
                            this.fetchResults();
                        }, 5000);
                    },

                    async fetchResults() {
                        try {
                            const response = await fetch('{{ route('elections.live', $election) }}');
                            const data = await response.json();
                            
                            this.totalVotes = data.total_votes;

                            if (data.has_ended) {
                                clearInterval(this.polling);
                                window.location.reload();
                                return;
                            }

                            @if($election->type === 'election')
                                data.data.forEach(position => {
                                    position.candidates.forEach(candidate => {
                                        const votesEl = document.getElementById('votes-' + candidate.id);
                                        const percentEl = document.getElementById('percentage-' + candidate.id);
                                        const barEl = document.getElementById('bar-' + candidate.id);
                                        
                                        if (votesEl) votesEl.textContent = candidate.votes + ' votes (' + candidate.percentage + '%)';
                                        if (percentEl) percentEl.textContent = candidate.percentage;
                                        if (barEl) barEl.style.width = candidate.percentage + '%';
                                    });
                                });
                            @else
                                data.data.options.forEach(option => {
                                    const votesEl = document.getElementById('poll-votes-' + option.id);
                                    const percentEl = document.getElementById('poll-percentage-' + option.id);
                                    const barEl = document.getElementById('poll-bar-' + option.id);
                                    
                                    if (votesEl) votesEl.textContent = option.votes + ' votes (' + option.percentage + '%)';
                                    if (percentEl) percentEl.textContent = option.percentage;
                                    if (barEl) barEl.style.width = option.percentage + '%';
                                });
                            @endif
                        } catch (e) {
                            console.error('Failed to fetch results:', e);
                        }
                    }
                }
            }
        </script>
        @endpush
    @endif
</x-app-layout>
