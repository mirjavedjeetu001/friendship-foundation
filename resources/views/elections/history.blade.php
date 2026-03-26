<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Election History
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($elections->isEmpty())
                <div class="text-center py-12 bg-gray-800 rounded-lg">
                    <p class="text-gray-400">No completed elections yet</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($elections as $election)
                        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition">
                            <div class="flex flex-wrap justify-between items-start gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $election->title_bn ?? $election->title }}</h3>
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $election->type === 'election' ? 'bg-purple-900 text-purple-300' : 'bg-blue-900 text-blue-300' }}">
                                            {{ $election->type === 'election' ? 'Committee' : 'Poll' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-400">
                                        <span>{{ $election->end_time->format('d M Y') }}</span>
                                        <span>{{ $election->total_votes }} votes</span>
                                        @if($election->type === 'election')
                                            <span>{{ $election->term_years }} year term</span>
                                        @endif
                                    </div>

                                    @if($election->type === 'election')
                                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @foreach($election->positions->take(4) as $position)
                                                @php $winner = $position->candidates->where('is_winner', true)->first(); @endphp
                                                @if($winner)
                                                    <div class="flex items-center space-x-2 p-2 bg-gray-700/50 rounded">
                                                        @if($winner->user->profile && $winner->user->profile->passport_photo)
                                                            <img src="{{ asset('storage/' . $winner->user->profile->passport_photo) }}" 
                                                                class="w-8 h-8 rounded-full object-cover">
                                                        @else
                                                            <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-sm text-gray-400">
                                                                {{ substr($winner->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <div class="min-w-0">
                                                            <div class="text-xs text-gray-500">{{ $position->name }}</div>
                                                            <div class="text-sm text-white truncate">{{ $winner->user->name }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        @php $topOption = $election->pollOptions->sortByDesc('votes_count')->first(); @endphp
                                        @if($topOption)
                                            <div class="mt-3 p-3 bg-gray-700/50 rounded">
                                                <span class="text-yellow-400 mr-2">🏆</span>
                                                <span class="text-white">{{ $topOption->option_text_bn ?? $topOption->option_text }}</span>
                                                <span class="text-gray-400 ml-2">({{ $topOption->vote_percentage }}%)</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <a href="{{ route('elections.results', $election) }}" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm whitespace-nowrap">
                                    View Full Results
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $elections->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
