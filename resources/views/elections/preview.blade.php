<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                {{ $election->title_bn ?? $election->title }}
            </h2>
            <a href="{{ route('elections.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Coming Soon Banner -->
            <div class="bg-gradient-to-r from-indigo-900 via-purple-900 to-pink-900 rounded-2xl p-8 mb-8 text-center relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full filter blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-40 h-40 bg-pink-500 rounded-full filter blur-3xl"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="text-6xl mb-4">🗳️</div>
                    <h1 class="text-3xl font-bold text-white mb-2">Voting Coming Soon!</h1>
                    <p class="text-indigo-200 mb-6">Get ready to cast your vote. Voting will start soon.</p>
                    
                    <!-- Countdown Timer -->
                    <div class="bg-black/30 backdrop-blur rounded-xl p-6 inline-block"
                        x-data="{ 
                            remaining: {{ (int) max(0, now()->diffInSeconds($election->start_time, false)) }},
                            init() {
                                setInterval(() => {
                                    if (this.remaining > 0) this.remaining--;
                                    else window.location.reload();
                                }, 1000);
                            }
                        }">
                        <div class="text-indigo-200 text-sm mb-2">Voting Starts In</div>
                        <div class="flex justify-center gap-4">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white font-mono" x-text="String(Math.floor(remaining / 86400)).padStart(2, '0')">00</div>
                                <div class="text-xs text-indigo-300">Days</div>
                            </div>
                            <div class="text-3xl text-indigo-400">:</div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white font-mono" x-text="String(Math.floor((remaining % 86400) / 3600)).padStart(2, '0')">00</div>
                                <div class="text-xs text-indigo-300">Hours</div>
                            </div>
                            <div class="text-3xl text-indigo-400">:</div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white font-mono" x-text="String(Math.floor((remaining % 3600) / 60)).padStart(2, '0')">00</div>
                                <div class="text-xs text-indigo-300">Minutes</div>
                            </div>
                            <div class="text-3xl text-indigo-400">:</div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white font-mono" x-text="String(Math.floor(remaining % 60)).padStart(2, '0')">00</div>
                                <div class="text-xs text-indigo-300">Seconds</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-sm text-indigo-200">
                        <div class="flex items-center justify-center gap-4 flex-wrap">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Starts: {{ $election->start_time->format('d M Y, h:i A') }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ends: {{ $election->end_time->format('d M Y, h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($election->description)
                <div class="bg-gray-800 rounded-lg p-4 mb-6">
                    <p class="text-gray-300">{{ $election->description_bn ?? $election->description }}</p>
                </div>
            @endif

            <!-- Candidates Preview -->
            @if($election->type === 'election')
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Candidates
                    </h2>
                    
                    <div class="space-y-6">
                        @foreach($election->positions as $position)
                            <div class="bg-gray-800 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-indigo-400 mb-4 flex items-center">
                                    <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center mr-3 text-sm font-bold text-white">
                                        {{ $loop->iteration }}
                                    </span>
                                    {{ $position->name_bn ?? $position->name }}
                                    <span class="ml-2 text-sm text-gray-500 font-normal">({{ $position->candidates->count() }} candidates)</span>
                                </h3>
                                
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($position->candidates as $candidate)
                                        <div class="bg-gray-700/50 rounded-xl p-4 text-center hover:bg-gray-700 transition-all border border-gray-700 hover:border-indigo-500/50">
                                            <div class="relative inline-block mb-3">
                                                @if($candidate->user->profile && $candidate->user->profile->passport_photo)
                                                    <img src="{{ asset('storage/' . $candidate->user->profile->passport_photo) }}" 
                                                        class="w-20 h-20 rounded-full object-cover border-3 border-gray-600 mx-auto">
                                                @else
                                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center mx-auto border-3 border-gray-600">
                                                        <span class="text-2xl text-white font-bold">{{ substr($candidate->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="font-semibold text-white text-sm leading-tight">{{ $candidate->user->name }}</div>
                                            @if($candidate->user->profile && $candidate->user->profile->occupation)
                                                <div class="text-xs text-gray-400 mt-1">{{ $candidate->user->profile->occupation }}</div>
                                            @endif
                                            @if($candidate->manifesto)
                                                <div class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $candidate->manifesto }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Poll Options Preview -->
                <div class="bg-gray-800 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Poll Options
                    </h3>
                    <div class="space-y-3">
                        @foreach($election->pollOptions as $index => $option)
                            <div class="p-4 bg-gray-700/50 rounded-lg border border-gray-600 flex items-center">
                                <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center mr-3 text-sm font-bold text-white">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-white">{{ $option->option_text_bn ?? $option->option_text }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Election Info -->
            <div class="mt-6 bg-gray-800/50 rounded-xl p-4 border border-gray-700">
                <div class="flex flex-wrap gap-4 justify-center text-sm text-gray-400">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        {{ $election->type === 'election' ? 'Committee Election' : 'Poll' }}
                    </span>
                    @if($election->term_years)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Term: {{ $election->term_years }} Year(s)
                        </span>
                    @endif
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Status: Upcoming
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
