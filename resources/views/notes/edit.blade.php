<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Modify Note"/>
    </x-slot>

    <div class="p-6 bg-white border-b border-gray-200">
        <form action="{{ route('notes.update', $note) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control"
                       value="{{ old('title', $note->title) }}">
            </div>

            <div class="mb-4">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control">
                    @foreach(['personal', 'general', 'products-or-services', 'structure', 'financial', 'human-resources', 'operations', 'sales-marketing', 'customers', 'technology', 'future-proofing', 'legal-licensing'] as $category)
                        <option value="{{ $category }}" {{ $note->category == $category ? 'selected' : '' }}>
                            {{ ucwords(str_replace('-', ' ', $category)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label">Content</label>
                <textarea name="content" id="content"
                          class="form-control">{{ old('content', $note->content) }}</textarea>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" name="is_pinned" id="is_pinned"
                       class="form-check-input" {{ $note->is_pinned ? 'checked' : '' }}>
                <label for="is_pinned" class="form-check-label">Pin this note</label>
            </div>

            <button type="submit" class="btn btn-primary">Update Note</button>
        </form>
    </div>
</x-app-layout>
