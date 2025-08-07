<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Prompts"/>
    </x-slot>
    <style>
        th {
            font-weight: bold !important;
        }
    </style>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <table class="table">
                <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                @foreach($settings as $setting)
                    <tr>
                        <td>{{ $setting->id }}.</td>
                        <td>
                            <a href="{{ route('settings.edit', $setting->id) }}" class="text-info">
                                {{ $setting->title }}
                            </a>
                        </td>
                        <td style="text-align:justify">
                            {{ $setting->description }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>
