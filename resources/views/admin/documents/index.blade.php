<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Document Management
            </h2>
            <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
                + Upload New Document
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-900/50 border border-green-700 text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-900/50 border border-red-700 text-red-400 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($documents->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-300">No Documents</h3>
                            <a href="{{ route('admin.documents.create') }}" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300">
                                Upload First Document →
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Document</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Size</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($documents as $document)
                                        <tr class="hover:bg-gray-700/50">
                                            <td class="px-6 py-4">
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
                                                    <span class="text-xl">{{ $icon }}</span>
                                                    <div>
                                                        <div class="text-sm font-medium text-white">{{ $document->title }}</div>
                                                        @if($document->title_bn)
                                                            <div class="text-xs text-gray-500">{{ $document->title_bn }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-700 text-gray-300">
                                                    {{ $document->type_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-400">
                                                {{ $document->file_size_formatted }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-400">
                                                {{ $document->created_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end space-x-2">
                                                    <a href="{{ route('documents.download', $document) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">
                                                        Download
                                                    </a>
                                                    <a href="{{ route('admin.documents.edit', $document) }}" class="text-yellow-400 hover:text-yellow-300 text-sm">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm" onclick="return confirm('Are you sure you want to delete?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $documents->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
