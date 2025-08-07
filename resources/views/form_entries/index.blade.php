<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Form Entries</h2>
    </x-slot>

    <div class="container">
        <table class="table-auto w-full">
            <thead>
            <tr>
                <th class="px-4 py-2">Form</th>
                <th class="px-4 py-2">User</th>
                <th class="px-4 py-2">Responses</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($formEntries as $entry)
                <tr>
                    <td class="border px-4 py-2">{{ $entry->form->title }}</td>
                    <td class="border px-4 py-2">{{ $entry->user->full_name() }}</td>
                    <td class="border px-4 py-2">{{ json_encode($entry->responses) }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('form_entries.show', $entry->id) }}"
                           class="bg-blue-500 text-white px-2 py-1 rounded">View</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
