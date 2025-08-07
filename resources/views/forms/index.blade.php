<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Forms</h2>
    </x-slot>

    <div class="container">
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold">Forms</h2>
            <a href="{{ route('forms.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create New Form</a>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="table-auto w-full">
            <thead>
            <tr>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($forms as $form)
                <tr>
                    <td class="border px-4 py-2">{{ $form->title }}</td>
                    <td class="border px-4 py-2">{{ $form->description }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('forms.edit', $form->id) }}"
                           class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                        <form action="{{ route('forms.destroy', $form->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
