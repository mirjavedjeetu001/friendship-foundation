<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Create New Election/Poll
            </h2>
            <a href="{{ route('admin.elections.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="electionForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg">
                    <p class="font-medium mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.elections.store') }}" class="space-y-6">
                @csrf

                <!-- Type Selection -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Select Type</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="election" x-model="type" class="sr-only peer">
                            <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-gray-700 hover:border-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-purple-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-white">Committee Election</div>
                                        <div class="text-sm text-gray-400">Elect candidates for various positions</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="poll" x-model="type" class="sr-only peer">
                            <div class="p-4 border-2 rounded-lg transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-900/30 border-gray-700 hover:border-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-blue-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-white">General Poll</div>
                                        <div class="text-sm text-gray-400">Question/opinion voting</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Title (English) *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                            @error('title')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Title (Bangla)</label>
                            <input type="text" name="title_bn" value="{{ old('title_bn') }}"
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Time Settings -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Time Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Start Time *</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">End Time *</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div x-show="type === 'election'">
                            <label class="block text-sm font-medium text-gray-300 mb-1">Term (Years) *</label>
                            <select name="term_years"
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                                <option value="1">1 Year</option>
                                <option value="2">2 Years</option>
                                <option value="3">3 Years</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Positions (for election) -->
                <div x-show="type === 'election'" class="bg-gray-800 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Positions & Candidates</h3>
                        <button type="button" @click="addPosition()" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                            + Add Position
                        </button>
                    </div>

                    <template x-for="(position, pIndex) in positions" :key="pIndex">
                        <div class="mb-6 p-4 bg-gray-700/50 rounded-lg">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-white font-medium" x-text="'Position #' + (pIndex + 1)"></span>
                                <button type="button" @click="removePosition(pIndex)" class="text-red-400 hover:text-red-300 text-sm" x-show="positions.length > 1">
                                    Remove
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Position Name (English) *</label>
                                    <input type="text" :name="'positions[' + pIndex + '][name]'" x-model="position.name" required
                                        placeholder="e.g., President"
                                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-400 mb-1">Position Name (Bangla)</label>
                                    <input type="text" :name="'positions[' + pIndex + '][name_bn]'" x-model="position.name_bn"
                                        placeholder="e.g., President"
                                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 text-white text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-2">Select Candidates *</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-64 overflow-y-auto p-2 bg-gray-800 rounded-lg border border-gray-600">
                                    @foreach($members as $member)
                                        <div class="cursor-pointer group"
                                            @click="toggleCandidate(pIndex, '{{ $member->id }}')">
                                            <template x-if="position.candidates.includes('{{ $member->id }}')">
                                                <input type="hidden" 
                                                    :name="'positions[' + pIndex + '][candidates][]'" 
                                                    value="{{ $member->id }}">
                                            </template>
                                            <div class="p-2 rounded-lg border-2 transition-all"
                                                :class="position.candidates.includes('{{ $member->id }}') ? 'border-indigo-500 bg-indigo-900/40' : 'border-gray-700 hover:border-gray-500 hover:bg-gray-700/50'">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" 
                                                        class="w-8 h-8 rounded-full object-cover border-2"
                                                        :class="position.candidates.includes('{{ $member->id }}') ? 'border-indigo-400' : 'border-gray-600'">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-medium text-white truncate">{{ $member->name }}</p>
                                                        <p class="text-[10px] text-gray-400">{{ $member->member_id }}</p>
                                                    </div>
                                                    <div x-show="position.candidates.includes('{{ $member->id }}')">
                                                        <svg class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="text-indigo-400" x-text="position.candidates.length"></span> selected
                                </p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Poll Options (for poll) -->
                <div x-show="type === 'poll'" class="bg-gray-800 rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Poll Options</h3>
                        <button type="button" @click="addOption()" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                            + Add Option
                        </button>
                    </div>

                    <template x-for="(option, oIndex) in options" :key="oIndex">
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="text-gray-400 w-6" x-text="(oIndex + 1) + '.'"></span>
                            <input type="text" :name="type === 'poll' ? 'options[]' : null" x-model="options[oIndex]" :required="type === 'poll'"
                                placeholder="Enter option"
                                class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded focus:ring-2 focus:ring-indigo-500 text-white text-sm">
                            <button type="button" @click="removeOption(oIndex)" class="text-red-400 hover:text-red-300" x-show="options.length > 2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Submit -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.elections.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function electionForm() {
            return {
                type: 'election',
                positions: [
                    { name: '', name_bn: '', candidates: [] }
                ],
                options: ['', ''],

                addPosition() {
                    this.positions.push({ name: '', name_bn: '', candidates: [] });
                },

                removePosition(index) {
                    if (this.positions.length > 1) {
                        this.positions.splice(index, 1);
                    }
                },

                toggleCandidate(posIndex, memberId) {
                    const idx = this.positions[posIndex].candidates.indexOf(memberId);
                    if (idx === -1) {
                        this.positions[posIndex].candidates.push(memberId);
                    } else {
                        this.positions[posIndex].candidates.splice(idx, 1);
                    }
                },

                addOption() {
                    this.options.push('');
                },

                removeOption(index) {
                    if (this.options.length > 2) {
                        this.options.splice(index, 1);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
