<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Election History
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Tabs -->
                    <div class="flex space-x-4 mb-6 border-b border-gray-700 pb-4">
                        <a href="{{ route('admin.elections.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm">
                            All Elections
                        </a>
                        <a href="{{ route('admin.elections.history') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                            History
                        </a>
                    </div>

                    @if($elections->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-400">No completed elections yet</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($elections as $election)
                                <div class="bg-gray-700/50 rounded-lg p-4 hover:bg-gray-700 transition">
                                    <div class="flex flex-wrap justify-between items-start gap-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-white">{{ $election->title_bn ?? $election->title }}</h3>
                                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                                <span class="text-gray-400 text-sm">
                                                    {{ $election->end_time->format('d M Y') }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs rounded-full {{ $election->type === 'election' ? 'bg-purple-900 text-purple-300' : 'bg-blue-900 text-blue-300' }}">
                                                    {{ $election->type === 'election' ? 'Committee' : 'Poll' }}
                                                </span>
                                                <span class="text-gray-400 text-sm">
                                                    {{ $election->total_votes }} votes
                                                </span>
                                            </div>
                                            @if($election->type === 'election' && $election->positions->count() > 0)
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach($election->positions->take(3) as $position)
                                                        @if($position->winner)
                                                            <div class="px-3 py-1 bg-gray-800 rounded-full text-sm">
                                                                <span class="text-gray-400">{{ $position->name }}:</span>
                                                                <span class="text-white">{{ $position->winner->user->name }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    @if($election->positions->count() > 3)
                                                        <span class="text-gray-500 text-sm py-1">+{{ $election->positions->count() - 3 }} more</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('admin.elections.results', $election) }}" 
                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
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
        </div>
    </div>
</x-app-layout>
