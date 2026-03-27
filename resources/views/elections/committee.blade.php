<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            About Allied Group
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Simple Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-8 text-center">
                <h1 class="text-3xl font-bold text-white mb-2">Allied Group</h1>
                <p class="text-indigo-200">United in Purpose, Strong in Action</p>
            </div>

            @php
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
                <!-- Committee Section -->
                <div class="bg-gray-800 rounded-2xl p-6 mb-8">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-white mb-1">Executive Committee</h2>
                        <p class="text-sm text-gray-400">
                            Term: {{ $latestElection->end_time->format('M Y') }} - {{ $latestElection->end_time->addYears($latestElection->term_years)->format('M Y') }}
                        </p>
                    </div>

                    @php 
                        $positions = $latestElection->positions;
                    @endphp

                    <!-- All Committee Members - Same Size -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($positions as $position)
                            @if($position->winner && $position->winner->votes_count > 0)
                                <div class="text-center p-4 bg-gray-700/50 rounded-xl">
                                    <div class="w-16 h-16 mx-auto rounded-full overflow-hidden border-2 border-indigo-500 mb-3">
                                        @if($position->winner->user->profile && $position->winner->user->profile->passport_photo)
                                            <img src="{{ asset('storage/' . $position->winner->user->profile->passport_photo) }}" 
                                                class="w-full h-full object-cover" alt="{{ $position->winner->user->name }}">
                                        @else
                                            <div class="w-full h-full bg-indigo-600 flex items-center justify-center">
                                                <span class="text-xl text-white font-bold">{{ substr($position->winner->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="inline-block px-2 py-0.5 bg-indigo-600 text-white text-xs rounded-full mb-1">
                                        {{ $position->name }}
                                    </span>
                                    <h4 class="text-sm font-medium text-white">{{ $position->winner->user->name }}</h4>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <!-- No Committee State -->
                <div class="bg-gray-800 rounded-2xl p-8 text-center mb-8">
                    <div class="w-16 h-16 mx-auto rounded-full bg-indigo-600/20 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Committee Coming Soon</h3>
                    <p class="text-gray-400">The executive committee will be announced after election results.</p>
                    
                    @if($latestElection && $latestElection->status === 'active')
                        <a href="{{ route('elections.show', $latestElection) }}" class="inline-block mt-4 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
                            Vote Now
                        </a>
                    @elseif($latestElection && $latestElection->status === 'upcoming')
                        <p class="mt-4 text-sm text-blue-400">Election starts {{ $latestElection->start_time->diffForHumans() }}</p>
                    @endif
                </div>
            @endif

            <!-- All Members Section -->
            @if(isset($allMembers) && $allMembers->count() > 0)
                <div class="bg-gray-800 rounded-2xl p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-white mb-1">All Members</h2>
                        <p class="text-sm text-gray-400">{{ $allMembers->count() }} members</p>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        @foreach($allMembers as $member)
                            <div class="text-center group">
                                <div class="w-14 h-14 mx-auto rounded-full overflow-hidden border-2 border-gray-600 group-hover:border-teal-500 transition-colors">
                                    @if($member->memberProfile && $member->memberProfile->passport_photo)
                                        <img src="{{ asset('storage/' . $member->memberProfile->passport_photo) }}" 
                                            class="w-full h-full object-cover" alt="{{ $member->name }}">
                                    @else
                                        <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                            <span class="text-lg text-gray-400">{{ substr($member->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-gray-300 leading-tight">{{ $member->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Past Committees -->
            @if($pastElections->count() > 0)
                <div class="mt-8 bg-gray-800 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Past Committees</h3>
                    <div class="space-y-3">
                        @foreach($pastElections as $election)
                            <div class="bg-gray-700/50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <div>
                                        <h4 class="font-medium text-white">{{ $election->title }}</h4>
                                        <p class="text-xs text-gray-500">
                                            {{ $election->end_time->format('M Y') }} - {{ $election->end_time->addYears($election->term_years)->format('M Y') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('elections.results', $election) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">
                                        Details
                                    </a>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($election->positions as $position)
                                        @php $winner = $position->candidates->where('is_winner', true)->first(); @endphp
                                        @if($winner)
                                            <span class="text-xs px-2 py-1 bg-gray-600 rounded text-gray-300">
                                                {{ $position->name }}: {{ $winner->user->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
