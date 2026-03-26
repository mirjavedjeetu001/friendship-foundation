<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Edit Document
            </h2>
            <a href="{{ route('admin.documents.index') }}" class="text-gray-400 hover:text-white">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.documents.update', $document) }}" enctype="multipart/form-data" class="bg-gray-800 rounded-lg p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Title (English) *</label>
                        <input type="text" name="title" value="{{ old('title', $document->title) }}" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                        @error('title')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Title (Bangla)</label>
                        <input type="text" name="title_bn" value="{{ old('title_bn', $document->title_bn) }}"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">{{ old('description', $document->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Type *</label>
                        <select name="type" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white">
                            <option value="deed" {{ old('type', $document->type) === 'deed' ? 'selected' : '' }}>📄 Deed</option>
                            <option value="resolution" {{ old('type', $document->type) === 'resolution' ? 'selected' : '' }}>📋 Resolution</option>
                            <option value="notice" {{ old('type', $document->type) === 'notice' ? 'selected' : '' }}>📢 Notice</option>
                            <option value="report" {{ old('type', $document->type) === 'report' ? 'selected' : '' }}>📊 Report</option>
                            <option value="other" {{ old('type', $document->type) === 'other' ? 'selected' : '' }}>📁 Other</option>
                        </select>
                    </div>

                    <div class="p-4 bg-gray-700/50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-300">Current File:</span>
                            <a href="{{ route('documents.download', $document) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">
                                Download
                            </a>
                        </div>
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
                                <div class="text-white">{{ basename($document->file_path) }}</div>
                                <div class="text-xs text-gray-500">{{ $document->file_size_formatted }}</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">New File (Optional)</label>
                        <input type="file" name="file"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white file:cursor-pointer">
                        <p class="mt-1 text-xs text-gray-500">New file will replace the existing one.</p>
                        @error('file')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('admin.documents.index') }}" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
