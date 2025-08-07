<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Setting
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('settings.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="setting_name" class="form-label">Setting Name</label>
                            <input type="text" name="setting_name" class="form-control" id="setting_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="setting_value" class="form-label">Setting Value</label>
                            <textarea name="setting_value" class="form-control" id="setting_value" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
