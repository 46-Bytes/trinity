<x-app-layout>
    <x-slot name="header">
        <x-page-header title="View Note"/>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1>{{ $note->title }}</h1>
                    <p><strong>Category: </strong>{{ ucwords(str_replace('-', ' ', $note->category)) }}</p>
                    <p><strong>Content: </strong>{{ $note->content }}</p>
                    <p><strong>Pinned: </strong>{{ $note->is_pinned ? 'Yes' : 'No' }}</p>
                    <p><strong>Created At: </strong>{{ $note->created_at->format('Y-m-d') }}</p>
                    <p><strong>Updated At: </strong>{{ $note->updated_at->format('Y-m-d') }}</p>
                    <a href="{{ route('notes.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
