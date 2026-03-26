<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                {{ $document->title_bn ?? $document->title }}
            </h2>
            <a href="{{ route('documents.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <!-- Document Header -->
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-start gap-4">
                        @php
                            $icon = match($document->file_type) {
                                'pdf' => '📕',
                                'doc', 'docx' => '📘',
                                'xls', 'xlsx' => '📗',
                                'jpg', 'jpeg', 'png' => '🖼️',
                                default => '📄',
                            };
                        @endphp
                        <span class="text-5xl">{{ $icon }}</span>
                        <div class="flex-1">
                            <span class="px-2 py-0.5 text-xs rounded bg-indigo-900 text-indigo-300 mb-2 inline-block">
                                {{ $document->type_label }}
                            </span>
                            <h1 class="text-2xl font-bold text-white mb-2">{{ $document->title_bn ?? $document->title }}</h1>
                            @if($document->description)
                                <p class="text-gray-400">{{ $document->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Document Info -->
                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 border-b border-gray-700 bg-gray-850">
                    <div>
                        <div class="text-gray-500 text-sm">File Type</div>
                        <div class="text-white uppercase">{{ $document->file_type }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">File Size</div>
                        <div class="text-white">{{ $document->file_size_formatted }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Upload Date</div>
                        <div class="text-white">{{ $document->created_at->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm">Uploaded By</div>
                        <div class="text-white">{{ $document->uploader->name ?? 'Unknown' }}</div>
                    </div>
                </div>

                <!-- Preview (for images and PDFs) -->
                @if(in_array($document->file_type, ['jpg', 'jpeg', 'png']))
                    <div class="p-6 bg-gray-900">
                        <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->title }}" class="max-w-full mx-auto rounded-lg shadow-lg">
                    </div>
                @elseif($document->file_type === 'pdf')
                    <div class="p-6">
                        <iframe src="{{ asset('storage/' . $document->file_path) }}" class="w-full h-[600px] rounded-lg" frameborder="0"></iframe>
                    </div>
                @endif

                <!-- Actions -->
                <div class="p-6 flex justify-center">
                    <a href="{{ route('documents.download', $document) }}" 
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Download</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
