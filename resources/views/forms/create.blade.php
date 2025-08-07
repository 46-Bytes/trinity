<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Create New Form</h2>
    </x-slot>

    <div class="container">
        <form action="{{ route('forms.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="title" class="block">Title:</label>
                <input type="text" name="title" id="title" class="w-full border border-gray-400 px-2 py-1"
                       value="{{ old('title') }}">
            </div>
            <div class="mb-4">
                <label for="slug" class="block">Slug:</label>
                <input type="text" name="slug" id="slug" class="w-full border border-gray-400 px-2 py-1"
                       value="{{ old('slug') }}">
            </div>

            <div class="mb-4">
                <label for="description" class="block">Description:</label>
                <textarea name="description" id="description"
                          class="w-full border border-gray-400 px-2 py-1">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="form_json" class="block">Form JSON (SurveyJS):</label>
                <textarea name="form_json" id="form_json"
                          class="w-full border border-gray-400 px-2 py-1">{{ old('form_json') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Form</button>
        </form>
    </div>
</x-app-layout>
