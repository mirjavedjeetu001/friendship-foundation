<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Documents
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter by Type -->
            <div class="bg-gray-800 rounded-lg p-4 mb-6">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('documents.index') }}" 
                        class="px-4 py-2 rounded-lg text-sm {{ !isset($type) ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        All
                    </a>
                    <a href="{{ route('documents.type', 'deed') }}" 
                        class="px-4 py-2 rounded-lg text-sm {{ isset($type) && $type === 'deed' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        📄 Deed
                    </a>
                    <a href="{{ route('documents.type', 'resolution') }}" 
                        class="px-4 py-2 rounded-lg text-sm {{ isset($type) && $type === 'resolution' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        📋 Resolution
                    </a>
                    <a href="{{ route('documents.type', 'notice') }}" 
                        class="px-4 py-2 rounded-lg text-sm {{ isset($type) && $type === 'notice' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        📢 Notice
                    </a>
                    <a href="{{ route('documents.type', 'report') }}" 
                        class="px-4 py-2 rounded-lg text-sm {{ isset($type) && $type === 'report' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        📊 Report
                    </a>
                    <a href="{{ route('documents.type', 'other') }}" 
                        class="px-4 py-2 rounded-lg text-sm {{ isset($type) && $type === 'other' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                        📁 Other
                    </a>
                </div>
            </div>

            @if($documents->isEmpty())
                <div class="text-center py-12 bg-gray-800 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-300">No Documents</h3>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($documents as $document)
                        <div class="bg-gray-800 rounded-lg overflow-hidden hover:ring-2 hover:ring-indigo-500 transition">
                            <div class="p-5">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        @php
                                            $icon = match($document->file_type) {
                                                'pdf' => '📕',
                                                'doc', 'docx' => '📘',
                                                'xls', 'xlsx' => '📗',
                                                'jpg', 'jpeg', 'png' => '🖼️',
                                                default => '📄',
                                            };
                                        @endphp
                                        <span class="text-2xl">{{ $icon }}</span>
                                        <div>
                                            <span class="px-2 py-0.5 text-xs rounded bg-gray-700 text-gray-400">
                                                {{ $document->type_label }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 uppercase">{{ $document->file_type }}</span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-white mb-2">
                                    {{ $document->title_bn ?? $document->title }}
                                </h3>
                                
                                @if($document->description)
                                    <p class="text-sm text-gray-400 mb-3">{{ Str::limit($document->description, 80) }}</p>
                                @endif

                                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                    <span>{{ $document->created_at->format('d M Y') }}</span>
                                    <span>{{ $document->file_size_formatted }}</span>
                                </div>

                                <div class="flex space-x-2">
                                    <a href="{{ route('documents.show', $document) }}" 
                                        class="flex-1 text-center py-2 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm">
                                        View
                                    </a>
                                    <a href="{{ route('documents.download', $document) }}" 
                                        class="flex-1 text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
