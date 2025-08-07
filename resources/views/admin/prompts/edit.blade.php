<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Prompt
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('settings.update', $setting->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" id="title"
                                   value="{{ $setting->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" id="description"
                                   value="{{ $setting->description }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="setting_name" class="form-label">Slug</label>
                            <input type="text" name="setting_name" class="form-control" id="setting_name"
                                   value="{{ $setting->setting_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="setting_value" class="form-label">Prompt</label>
                            <textarea name="setting_value" class="form-control" id="setting_value" required>{{ $setting->setting_value }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Include SimpleMDE from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

    <script>
        // Initialize SimpleMDE on the textarea
        var simplemde = new SimpleMDE({element: document.getElementById("setting_value")});
    </script>
</x-app-layout>
