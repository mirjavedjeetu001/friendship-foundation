<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Results: {{ $election->title_bn ?? $election->title }}
            </h2>
            <a href="{{ route('admin.elections.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Publication Status & Controls -->
            <div class="bg-gray-800 rounded-lg p-4 mb-6 flex flex-wrap justify-between items-center gap-4">
                <div class="flex items-center space-x-3">
                    @if($election->results_published)
                        <span class="flex items-center text-green-400">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Results Published
                        </span>
                        <span class="text-gray-500 text-sm">
                            {{ $election->results_published_at ? $election->results_published_at->format('d M Y, h:i A') : '' }}
                        </span>
                    @else
                        <span class="flex items-center text-yellow-400">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Pending Publication
                        </span>
                        <span class="text-gray-500 text-sm">Results not visible to members yet</span>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    @if($election->results_published)
                        <form action="{{ route('admin.elections.unpublish', $election) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition-colors" onclick="return confirm('Unpublish results? Committee page will hide this election.')">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                                Unpublish
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.elections.publish', $election) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors" onclick="return confirm('Publish results? This will show winners on Committee page.')">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Publish Results
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-gradient-to-r from-indigo-900 to-purple-900 rounded-lg p-6 mb-6">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">Election Completed</h3>
                    <p class="text-indigo-200">{{ $election->end_time->format('d M Y, h:i A') }}</p>
                    <div class="mt-4 inline-flex items-center space-x-6">
                        <div>
                            <div class="text-3xl font-bold text-white">{{ $election->total_votes }}</div>
                            <div class="text-indigo-200 text-sm">Total Votes</div>
                        </div>
                        @if($election->type === 'election')
                            <div>
                                <div class="text-3xl font-bold text-white">{{ $election->positions->count() }}</div>
                                <div class="text-indigo-200 text-sm">Positions</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($election->type === 'election')
                <!-- Winners -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-white mb-6 text-center">🏆 Elected Committee</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($election->positions as $position)
                            @php $winner = $position->candidates->where('is_winner', true)->first(); @endphp
                            @if($winner)
                                <div class="text-center p-4 bg-gradient-to-b from-gray-700 to-gray-800 rounded-lg border border-gray-600">
                                    <div class="w-20 h-20 mx-auto mb-3 rounded-full overflow-hidden border-4 border-yellow-500">
                                        @if($winner->user->profile && $winner->user->profile->passport_photo)
                                            <img src="{{ asset('storage/' . $winner->user->profile->passport_photo) }}" 
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-600 flex items-center justify-center">
                                                <span class="text-2xl text-gray-400">{{ substr($winner->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-yellow-400 font-medium text-sm mb-1">{{ $position->name_bn ?? $position->name }}</div>
                                    <div class="text-white font-semibold">{{ $winner->user->name }}</div>
                                    <div class="text-gray-400 text-sm mt-1">{{ $winner->votes_count }} votes ({{ $winner->vote_percentage }}%)</div>
                                </div>
                            @else
                                <div class="text-center p-4 bg-gray-700/30 rounded-lg border border-gray-600 border-dashed">
                                    <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-700 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="text-gray-500 font-medium text-sm mb-1">{{ $position->name_bn ?? $position->name }}</div>
                                    <div class="text-gray-400 text-sm">No winner selected</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- All Results with Toggle -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Full Results</h3>
                        <span class="text-gray-400 text-sm">Click 🏆 to toggle winner</span>
                    </div>
                    <div class="space-y-6">
                        @foreach($election->positions as $position)
                            <div class="border border-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-indigo-400 mb-4">{{ $position->name_bn ?? $position->name }}</h4>
                                <div class="space-y-3">
                                    @foreach($position->candidates->sortByDesc('votes_count') as $index => $candidate)
                                        <div class="flex items-center space-x-4">
                                            <span class="w-6 text-center {{ $index === 0 ? 'text-yellow-400 font-bold' : 'text-gray-500' }}">
                                                {{ $index + 1 }}
                                            </span>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-white {{ $candidate->is_winner ? 'font-semibold' : '' }}">
                                                        {{ $candidate->user->name }}
                                                        <form action="{{ route('admin.elections.toggle-winner', [$election, $candidate]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="ml-2 hover:scale-125 transition-transform" title="{{ $candidate->is_winner ? 'Remove as winner' : 'Set as winner' }}">
                                                                @if($candidate->is_winner)
                                                                    🏆
                                                                @else
                                                                    <span class="opacity-30 hover:opacity-100">🏆</span>
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </span>
                                                    <span class="text-gray-400">{{ $candidate->votes_count }} votes</span>
                                                </div>
                                                <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-full {{ $candidate->is_winner ? 'bg-yellow-500' : 'bg-indigo-600' }} rounded-full transition-all" 
                                                        style="width: {{ $candidate->vote_percentage }}%"></div>
                                                </div>
                                            </div>
                                            <span class="w-14 text-right text-gray-400 text-sm">{{ $candidate->vote_percentage }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Poll Results -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-6">Poll Results</h3>
                    <div class="space-y-4">
                        @foreach($election->pollOptions->sortByDesc('votes_count') as $index => $option)
                            <div class="p-4 {{ $index === 0 ? 'bg-indigo-900/30 border border-indigo-700' : 'bg-gray-700/30' }} rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-white font-medium">
                                        @if($index === 0)
                                            🏆
                                        @endif
                                        {{ $option->option_text_bn ?? $option->option_text }}
                                    </span>
                                    <span class="text-gray-400">{{ $option->votes_count }} votes ({{ $option->vote_percentage }}%)</span>
                                </div>
                                <div class="h-3 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $index === 0 ? 'bg-yellow-500' : 'bg-indigo-600' }} rounded-full transition-all" 
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
