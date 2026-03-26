<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Elections & Voting
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-900/50 border border-blue-700 text-blue-400 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Active Elections -->
            @if($activeElections->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></span>
                        Active Elections - Vote Now!
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($activeElections as $election)
                            <div class="bg-gradient-to-br from-green-900/40 to-emerald-900/20 rounded-2xl p-6 border border-green-600/30 shadow-lg shadow-green-900/20">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-2xl font-bold text-white">{{ $election->title_bn ?? $election->title }}</h4>
                                        <span class="inline-flex items-center text-sm text-green-400 mt-1">
                                            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></span>
                                            Voting in Progress
                                        </span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $election->type === 'election' ? 'bg-purple-600/30 text-purple-300 border border-purple-500/30' : 'bg-blue-600/30 text-blue-300 border border-blue-500/30' }}">
                                        {{ $election->type === 'election' ? 'Committee Election' : 'Poll' }}
                                    </span>
                                </div>
                                
                                @if($election->description)
                                    <p class="text-gray-400 text-sm mb-4">{{ Str::limit($election->description_bn ?? $election->description, 150) }}</p>
                                @endif

                                <!-- Time Remaining -->
                                <div class="bg-gray-900/50 rounded-xl p-4 mb-4" x-data="countdownTimer({{ $election->end_time->timestamp * 1000 }})" x-init="startTimer()">
                                    <div class="text-center mb-2 text-gray-400 text-sm">Time Remaining</div>
                                    <div class="flex justify-center gap-3">
                                        <div class="text-center">
                                            <div class="bg-gradient-to-b from-red-600 to-red-800 rounded-lg px-3 py-2 min-w-[50px]">
                                                <span class="text-2xl font-bold text-white" x-text="hours.toString().padStart(2, '0')">00</span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">Hours</div>
                                        </div>
                                        <div class="text-2xl font-bold text-gray-500 self-start pt-2">:</div>
                                        <div class="text-center">
                                            <div class="bg-gradient-to-b from-red-600 to-red-800 rounded-lg px-3 py-2 min-w-[50px]">
                                                <span class="text-2xl font-bold text-white" x-text="minutes.toString().padStart(2, '0')">00</span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">Minutes</div>
                                        </div>
                                        <div class="text-2xl font-bold text-gray-500 self-start pt-2">:</div>
                                        <div class="text-center">
                                            <div class="bg-gradient-to-b from-red-600 to-red-800 rounded-lg px-3 py-2 min-w-[50px]">
                                                <span class="text-2xl font-bold text-white" x-text="seconds.toString().padStart(2, '0')">00</span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">Seconds</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                                    <span>{{ $election->total_votes }} votes cast</span>
                                    @if($election->type === 'election')
                                        <span>{{ $election->positions->count() }} positions</span>
                                    @endif
                                </div>

                                @if($election->hasUserVoted(auth()->id()))
                                    <a href="{{ route('elections.results', $election) }}" 
                                        class="block w-full text-center py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-medium transition-all shadow-lg">
                                        ✓ You Voted - View Live Results
                                    </a>
                                @else
                                    <a href="{{ route('elections.vote', $election) }}" 
                                        class="block w-full text-center py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-bold text-lg transition-all shadow-lg shadow-green-900/30">
                                        🗳️ Cast Your Vote Now
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upcoming Elections with Candidates -->
            @if($upcomingElections->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Upcoming Elections
                    </h3>
                    
                    @foreach($upcomingElections as $election)
                        <div class="bg-gradient-to-br from-indigo-900/30 via-purple-900/20 to-gray-900 rounded-2xl border border-indigo-600/30 overflow-hidden mb-6 shadow-xl">
                            <!-- Header -->
                            <div class="p-6 border-b border-indigo-700/30">
                                <div class="flex flex-wrap justify-between items-start gap-4">
                                    <div>
                                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-yellow-600/30 text-yellow-300 border border-yellow-500/30 mb-2">
                                            Coming Soon
                                        </span>
                                        <h4 class="text-2xl font-bold text-white">{{ $election->title_bn ?? $election->title }}</h4>
                                        @if($election->description)
                                            <p class="text-gray-400 mt-2">{{ $election->description_bn ?? $election->description }}</p>
                                        @endif
                                    </div>
                                    
                                    <!-- Countdown Timer -->
                                    <div class="bg-gray-900/70 rounded-xl p-4 border border-indigo-500/20" 
                                         x-data="countdownTimer({{ $election->start_time->timestamp * 1000 }})" 
                                         x-init="startTimer()">
                                        <div class="text-center text-xs text-indigo-300 mb-2">Starts In</div>
                                        <div class="flex gap-2">
                                            <div class="text-center">
                                                <div class="bg-gradient-to-b from-indigo-600 to-indigo-800 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                    <span class="text-lg font-bold text-white" x-text="days.toString().padStart(2, '0')">00</span>
                                                </div>
                                                <div class="text-[10px] text-gray-500 mt-1">Days</div>
                                            </div>
                                            <span class="text-indigo-400 self-start pt-1">:</span>
                                            <div class="text-center">
                                                <div class="bg-gradient-to-b from-indigo-600 to-indigo-800 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                    <span class="text-lg font-bold text-white" x-text="hours.toString().padStart(2, '0')">00</span>
                                                </div>
                                                <div class="text-[10px] text-gray-500 mt-1">Hrs</div>
                                            </div>
                                            <span class="text-indigo-400 self-start pt-1">:</span>
                                            <div class="text-center">
                                                <div class="bg-gradient-to-b from-indigo-600 to-indigo-800 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                    <span class="text-lg font-bold text-white" x-text="minutes.toString().padStart(2, '0')">00</span>
                                                </div>
                                                <div class="text-[10px] text-gray-500 mt-1">Min</div>
                                            </div>
                                            <span class="text-indigo-400 self-start pt-1">:</span>
                                            <div class="text-center">
                                                <div class="bg-gradient-to-b from-indigo-600 to-indigo-800 rounded-lg px-2 py-1.5 min-w-[40px]">
                                                    <span class="text-lg font-bold text-white" x-text="seconds.toString().padStart(2, '0')">00</span>
                                                </div>
                                                <div class="text-[10px] text-gray-500 mt-1">Sec</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-4 mt-4 text-sm text-gray-400">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Starts: {{ $election->start_time->format('d M Y, h:i A') }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Ends: {{ $election->end_time->format('d M Y, h:i A') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Candidates by Position -->
                            @if($election->type === 'election' && $election->positions->count() > 0)
                                <div class="p-6">
                                    <h5 class="text-lg font-semibold text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Candidates Standing for Election
                                    </h5>
                                    
                                    <div class="space-y-6">
                                        @foreach($election->positions as $position)
                                            <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700/50">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h6 class="text-lg font-medium text-indigo-400">
                                                        {{ $position->name_bn ?? $position->name }}
                                                    </h6>
                                                    <span class="text-sm text-gray-500">{{ $position->candidates->count() }} candidates</span>
                                                </div>
                                                
                                                @if($position->candidates->count() > 0)
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                                        @foreach($position->candidates as $candidate)
                                                            <div class="text-center group">
                                                                <div class="relative inline-block">
                                                                    <div class="w-16 h-16 mx-auto rounded-full overflow-hidden border-3 border-gray-600 group-hover:border-indigo-500 transition-all shadow-lg">
                                                                        @if($candidate->user->profile && $candidate->user->profile->passport_photo)
                                                                            <img src="{{ asset('storage/' . $candidate->user->profile->passport_photo) }}" 
                                                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                                                        @else
                                                                            <div class="w-full h-full bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center">
                                                                                <span class="text-xl text-white font-bold">{{ substr($candidate->user->name, 0, 1) }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-gray-800">
                                                                        {{ $loop->iteration }}
                                                                    </div>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <div class="text-sm font-medium text-white truncate">{{ $candidate->user->name }}</div>
                                                                    @if($candidate->user->profile && $candidate->user->profile->member_id)
                                                                        <div class="text-xs text-gray-500">{{ $candidate->user->profile->member_id }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-center text-gray-500 py-4">No candidates yet</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Recent Completed -->
            @if($completedElections->isNotEmpty())
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Recent Completed Elections
                        </h3>
                        <a href="{{ route('elections.history') }}" class="text-indigo-400 hover:text-indigo-300 text-sm flex items-center">
                            View All History
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                    <div class="bg-gray-800/50 rounded-xl overflow-hidden border border-gray-700/50">
                        <div class="divide-y divide-gray-700/50">
                            @foreach($completedElections as $election)
                                <div class="p-4 hover:bg-gray-700/30 transition-colors">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-white">{{ $election->title_bn ?? $election->title }}</h4>
                                                <div class="flex items-center gap-3 mt-1 text-sm text-gray-400">
                                                    <span>{{ $election->end_time->format('d M Y') }}</span>
                                                    <span>•</span>
                                                    <span>{{ $election->total_votes }} votes</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('elections.results', $election) }}" 
                                            class="px-4 py-2 bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 rounded-lg text-sm border border-indigo-500/30 transition-colors">
                                            View Results
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($activeElections->isEmpty() && $upcomingElections->isEmpty() && $completedElections->isEmpty())
                <div class="text-center py-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl border border-gray-700">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-700/50 flex items-center justify-center mb-4">
                        <svg class="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-300">No Elections Yet</h3>
                    <p class="mt-2 text-gray-500">New elections will appear here when created by admin</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function countdownTimer(targetTime) {
            return {
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0,
                interval: null,
                
                startTimer() {
                    this.updateTimer();
                    this.interval = setInterval(() => this.updateTimer(), 1000);
                },
                
                updateTimer() {
                    const now = Date.now();
                    const diff = Math.max(0, targetTime - now);
                    
                    if (diff === 0 && this.interval) {
                        clearInterval(this.interval);
                        location.reload(); // Reload when countdown ends
                        return;
                    }
                    
                    this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                }
            }
        }
    </script>
</x-app-layout>
