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
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Timer -->
            <div class="bg-gradient-to-r from-green-900 to-emerald-900 rounded-lg p-4 mb-6 text-center" 
                x-data="{ 
                    remaining: {{ (int) max(0, now()->diffInSeconds($election->end_time, false)) }},
                    init() {
                        setInterval(() => {
                            if (this.remaining > 0) this.remaining--;
                            else window.location.reload();
                        }, 1000);
                    }
                }">
                <div class="text-green-200 text-sm mb-1">Voting Ends In</div>
                <div class="text-3xl font-bold text-white font-mono tracking-wider">
                    <span x-text="String(Math.floor(remaining / 3600)).padStart(2, '0')"></span>
                    <span class="text-green-300">:</span>
                    <span x-text="String(Math.floor((remaining % 3600) / 60)).padStart(2, '0')"></span>
                    <span class="text-green-300">:</span>
                    <span x-text="String(Math.floor(remaining % 60)).padStart(2, '0')"></span>
                </div>
                <div class="text-xs text-green-300 mt-1">Hours : Minutes : Seconds</div>
            </div>

            @if($hasVoted)
                <div class="bg-blue-900/50 border border-blue-700 rounded-lg p-6 text-center mb-6">
                    <div class="text-4xl mb-2">✓</div>
                    <h3 class="text-xl font-semibold text-white mb-2">You have already voted!</h3>
                    <p class="text-blue-300 mb-4">Your vote has been submitted successfully.</p>
                    <a href="{{ route('elections.results', $election) }}" 
                        class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        View Live Results
                    </a>
                </div>
            @else
                <form method="POST" action="{{ route('elections.vote', $election) }}" id="voteForm">
                    @csrf

                    @if($election->description)
                        <div class="bg-gray-800 rounded-lg p-4 mb-6">
                            <p class="text-gray-300">{{ $election->description_bn ?? $election->description }}</p>
                        </div>
                    @endif

                    @if($election->type === 'election')
                        <!-- Committee Election -->
                        <div class="space-y-6">
                            @foreach($election->positions as $position)
                                <div class="bg-gray-800 rounded-lg p-6" x-data="{ selected: '{{ $userVotes[$position->id] ?? '' }}' }">
                                    <h3 class="text-lg font-semibold text-indigo-400 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $position->name_bn ?? $position->name }}
                                        <span class="text-sm text-gray-500 font-normal ml-2">(Select One)</span>
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($position->candidates as $candidate)
                                            <div class="relative cursor-pointer" 
                                                @click="selected = '{{ $candidate->id }}'">
                                                <input type="radio" 
                                                    name="votes[{{ $position->id }}]" 
                                                    value="{{ $candidate->id }}" 
                                                    x-model="selected"
                                                    class="sr-only" 
                                                    required>
                                                <div class="p-4 border-2 rounded-xl transition-all duration-200"
                                                    :class="selected == '{{ $candidate->id }}' ? 'border-indigo-500 bg-indigo-900/40 shadow-lg shadow-indigo-500/20' : 'border-gray-700 hover:border-gray-500 hover:bg-gray-700/50'">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex-shrink-0 relative">
                                                            @if($candidate->user->profile && $candidate->user->profile->passport_photo)
                                                                <img src="{{ asset('storage/' . $candidate->user->profile->passport_photo) }}" 
                                                                    class="w-16 h-16 rounded-full object-cover border-2"
                                                                    :class="selected == '{{ $candidate->id }}' ? 'border-indigo-400' : 'border-gray-600'">
                                                            @else
                                                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center border-2"
                                                                    :class="selected == '{{ $candidate->id }}' ? 'border-indigo-400' : 'border-gray-600'">
                                                                    <span class="text-xl text-gray-300 font-semibold">{{ substr($candidate->user->name, 0, 1) }}</span>
                                                                </div>
                                                            @endif
                                                            <!-- Checkmark overlay -->
                                                            <div x-show="selected == '{{ $candidate->id }}'" 
                                                                class="absolute -bottom-1 -right-1 w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-white font-semibold">{{ $candidate->user->name }}</div>
                                                            @if($candidate->user->profile && $candidate->user->profile->occupation)
                                                                <div class="text-sm text-gray-400">{{ $candidate->user->profile->occupation }}</div>
                                                            @endif
                                                            @if($candidate->manifesto)
                                                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $candidate->manifesto }}</div>
                                                            @endif
                                                        </div>
                                                        <!-- Selection indicator -->
                                                        <div class="flex-shrink-0">
                                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                                                :class="selected == '{{ $candidate->id }}' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-600'">
                                                                <svg x-show="selected == '{{ $candidate->id }}'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
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
                        <!-- Poll -->
                        <div class="bg-gray-800 rounded-lg p-6" x-data="{ selected: '' }">
                            <h3 class="text-lg font-semibold text-white mb-4">Select Your Choice</h3>
                            <div class="space-y-3">
                                @foreach($election->pollOptions as $option)
                                    <div class="relative cursor-pointer" @click="selected = '{{ $option->id }}'">
                                        <input type="radio" name="option_id" value="{{ $option->id }}" 
                                            x-model="selected"
                                            class="sr-only" required>
                                        <div class="p-4 border-2 rounded-lg transition-all flex items-center justify-between"
                                            :class="selected == '{{ $option->id }}' ? 'border-indigo-500 bg-indigo-900/30' : 'border-gray-700 hover:border-gray-600'">
                                            <span class="text-white">{{ $option->option_text_bn ?? $option->option_text }}</span>
                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                                                :class="selected == '{{ $option->id }}' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-600'">
                                                <svg x-show="selected == '{{ $option->id }}'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-bold text-lg shadow-lg shadow-green-500/30 transition-all"
                            onclick="return confirm('Are you sure you want to submit your vote? Once submitted, it cannot be changed.')">
                            <span class="flex items-center justify-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Submit Vote
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
