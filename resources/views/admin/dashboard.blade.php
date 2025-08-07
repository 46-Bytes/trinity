@php use App\Enums\GPTFilePurpose;use App\Helpers\GPT;use Illuminate\Support\Facades\Log;use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Admin Dashboard"/>
    </x-slot>

    <p>Welcome, {{ Auth::user()->name }}!</p>
    @php
        //        $gpt = new GPT();
        //        $gpt->syncFiles();
        //        dd($gpt->listFiles());
    @endphp
</x-app-layout>
