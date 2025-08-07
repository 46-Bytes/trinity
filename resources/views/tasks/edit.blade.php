@php use App\Enums\Category; @endphp
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Modify Task"/>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="title" class="block text-gray-700">Task Title</label>
                        <input type="text" name="title" id="title" class="w-full p-2 border border-gray-300 rounded"
                               value="{{ $task->title }}">
                    </div>

                    <div class="mb-4">
                        <label for="category" class="block text-gray-700">Category</label>
                        <select name="category" id="category" class="w-full p-2 border border-gray-300 rounded">
                            @foreach(Category::labels() as $category => $label)
                                <option value="{{ $category }}"
                                        @if($task->category == $category) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="date_due" class="block text-gray-700">Due Date</label>
                        <input type="date" name="date_due" id="date_due"
                               class="w-full p-2 border border-gray-300 rounded" value="{{ $task->date_due }}">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Description</label>
                        <textarea name="description" id="description"
                                  class="w-full p-2 border border-gray-300 rounded">{{ $task->description }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
