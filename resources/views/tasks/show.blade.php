<x-app-layout>
    <x-slot name="header">
        <x-page-header title="View Task"/>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">{{ $task->title }}</h3>

                <p><strong>Category:</strong> {{ $task->category }}</p>
                <p><strong>Due Date:</strong> {{ $task->date_due }}</p>
                <p><strong>Description:</strong> {{ $task->description }}</p>

                <div class="flex justify-end">
                    <a href="{{ route('tasks.edit', $task->id) }}"
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Edit Task
                    </a>
                </div>
            </div>
        </div>
    </div
