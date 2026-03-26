<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            About Allied Group - Committee
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @php
                // Check if latest election has actual winners with votes AND results are published
                $hasValidCommittee = false;
                if ($latestElection && $latestElection->status === 'completed' && $latestElection->results_published) {
                    foreach ($latestElection->positions as $position) {
                        if ($position->winner && $position->winner->votes_count > 0) {
                            $hasValidCommittee = true;
                            break;
                        }
                    }
                }
            @endphp

            @if($hasValidCommittee)
                <!-- Current Committee -->
                <div class="bg-gradient-to-br from-indigo-900/50 to-purple-900/50 rounded-2xl p-8 mb-8 border border-indigo-700/30">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600/30 mb-4">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-2">Current Executive Committee</h2>
                        <p class="text-indigo-300">
                            Term: {{ $latestElection->end_time->format('M Y') }} - {{ $latestElection->end_time->addYears($latestElection->term_years)->format('M Y') }}
                        </p>
                    </div>

                    <!-- Tree Structure -->
                    <div class="relative">
                        @php 
                            $positions = $latestElection->positions;
                            $president = $positions->where('name', 'President')->first() ?? $positions->first();
                        @endphp

                        @if($president && $president->winner && $president->winner->votes_count > 0)
                            <!-- President/Top Position -->
                            <div class="flex justify-center mb-8">
                                <div class="text-center group">
                                    <div class="relative inline-block">
                                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-yellow-500 shadow-xl shadow-yellow-500/30 group-hover:scale-105 transition-transform">
                                            @if($president->winner->user->profile && $president->winner->user->profile->passport_photo)
                                                <img src="{{ asset('storage/' . $president->winner->user->profile->passport_photo) }}" 
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-yellow-600 to-yellow-800 flex items-center justify-center">
                                                    <span class="text-4xl text-white font-bold">{{ substr($president->winner->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 px-4 py-1.5 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white text-sm font-semibold rounded-full shadow-lg">
                                            {{ $president->name_bn ?? $president->name }}
                                        </span>
                                    </div>
                                    <div class="mt-5">
                                        <div class="text-xl font-bold text-white">{{ $president->winner->user->name }}</div>
                                        @if($president->winner->user->profile && $president->winner->user->profile->occupation)
                                            <div class="text-sm text-gray-400">{{ $president->winner->user->profile->occupation }}</div>
                                        @endif
                                        <div class="text-xs text-indigo-400 mt-1">{{ $president->winner->votes_count }} votes</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Connecting line -->
                            <div class="flex justify-center mb-4">
                                <div class="w-1 h-10 bg-gradient-to-b from-yellow-500 to-indigo-500 rounded-full"></div>
                            </div>

                            <!-- Horizontal line -->
                            <div class="flex justify-center mb-4">
                                <div class="w-4/5 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent rounded-full"></div>
                            </div>

                            <!-- Other Positions -->
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-{{ min($positions->count() - 1, 4) }} gap-6">
                                @foreach($positions->skip(1) as $position)
                                    @if($position->winner && $position->winner->votes_count > 0)
                                        <div class="text-center group">
                                            <!-- Vertical line from horizontal -->
                                            <div class="w-1 h-8 bg-indigo-500 mx-auto mb-3 rounded-full"></div>
                                            
                                            <div class="relative">
                                                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden border-3 border-indigo-500 shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                                                    @if($position->winner->user->profile && $position->winner->user->profile->passport_photo)
                                                        <img src="{{ asset('storage/' . $position->winner->user->profile->passport_photo) }}" 
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center">
                                                            <span class="text-2xl text-white font-bold">{{ substr($position->winner->user->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full whitespace-nowrap shadow">
                                                    {{ $position->name_bn ?? $position->name }}
                                                </span>
                                            </div>
                                            <div class="mt-4">
                                                <div class="text-sm font-semibold text-white">{{ $position->winner->user->name }}</div>
                                                @if($position->winner->user->profile && $position->winner->user->profile->occupation)
                                                    <div class="text-xs text-gray-400">{{ $position->winner->user->profile->occupation }}</div>
                                                @endif
                                                <div class="text-xs text-indigo-400 mt-1">{{ $position->winner->votes_count }} votes</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-12 text-center mb-8 border border-gray-700">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-600/20 mb-6">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No Committee Formed Yet</h3>
                    <p class="text-gray-400 max-w-md mx-auto">The executive committee will be displayed here after an election is completed with votes.</p>
                    
                    @if($latestElection && $latestElection->status === 'active')
                        <div class="mt-6">
                            <a href="{{ route('elections.show', $latestElection) }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Vote Now
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Past Committees History -->
            @if($pastElections->count() > 0)
                <div class="bg-gray-800 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-white mb-6">Past Committees</h3>
                    <div class="space-y-6">
                        @foreach($pastElections as $election)
                            <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                <div class="flex flex-wrap justify-between items-start gap-2 mb-3">
                                    <div>
                                        <h4 class="font-medium text-white">{{ $election->title_bn ?? $election->title }}</h4>
                                        <p class="text-sm text-gray-400">
                                            {{ $election->end_time->format('M Y') }} - {{ $election->end_time->addYears($election->term_years)->format('M Y') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('elections.results', $election) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">
                                        Details →
                                    </a>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($election->positions as $position)
                                        @php $winner = $position->candidates->where('is_winner', true)->first(); @endphp
                                        @if($winner)
                                            <div class="flex items-center space-x-2 px-3 py-1 bg-gray-700/50 rounded-full text-sm">
                                                <span class="text-gray-400">{{ $position->name }}:</span>
                                                <span class="text-white">{{ $winner->user->name }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- All Other Members Tree -->
            @if(isset($otherMembers) && $otherMembers->count() > 0)
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg p-8 border border-gray-700/50">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">All Members</h2>
                        <p class="text-gray-400">Total {{ $otherMembers->count() }} members</p>
                    </div>

                    <!-- Members Tree Structure -->
                    <div class="relative">
                        <!-- Connecting line from committee to members -->
                        @if($latestElection && $latestElection->positions->count() > 0)
                            <div class="flex justify-center mb-6">
                                <div class="w-px h-8 bg-gradient-to-b from-indigo-500 to-gray-500"></div>
                            </div>
                        @endif

                        <!-- Top horizontal line -->
                        <div class="flex justify-center mb-4">
                            <div class="w-full max-w-4xl h-px bg-gray-600"></div>
                        </div>

                        <!-- Members Grid with vertical connecting lines -->
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-4">
                            @foreach($otherMembers as $member)
                                <div class="text-center group">
                                    <!-- Vertical line from horizontal -->
                                    <div class="w-px h-4 bg-gray-600 mx-auto mb-2"></div>
                                    
                                    <div class="relative">
                                        <div class="w-14 h-14 mx-auto rounded-full overflow-hidden border-2 border-gray-600 group-hover:border-teal-500 transition-all shadow-md">
                                            @if($member->memberProfile && $member->memberProfile->passport_photo)
                                                <img src="{{ asset('storage/' . $member->memberProfile->passport_photo) }}" 
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                                    <span class="text-lg text-gray-400">{{ substr($member->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="text-xs font-medium text-gray-300 px-1 leading-tight">
                                            {{ $member->name }}
                                        </div>
                                        @if($member->memberProfile && $member->memberProfile->member_id)
                                            <div class="text-[10px] text-gray-500">{{ $member->memberProfile->member_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
