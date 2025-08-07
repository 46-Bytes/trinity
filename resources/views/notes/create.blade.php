<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Create Note"/>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('notes.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
                        </div>

                        <div class="mb-4">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">-- Select Category --</option>
                                @foreach(['personal', 'general', 'products-or-services', 'structure', 'financial', 'human-resources', 'operations', 'sales-marketing', 'customers', 'technology', 'future-proofing', 'legal-licensing'] as $category)
                                    <option value="{{ $category }}">{{ ucwords(str_replace('-', ' ', $category)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" id="content" class="form-control">{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="is_pinned" id="is_pinned" class="form-check-input">
                            <label for="is_pinned" class="form-check-label">Pin this note</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Note</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
