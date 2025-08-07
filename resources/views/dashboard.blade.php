@php
    use App\Models\Diagnostic;
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Welcome, {{ Auth::user()->name }}!"/>
    </x-slot>
    <style>

        .diagnostic-success {
            background: linear-gradient(90deg, #32CD32, #00B140); /* Subtle gradient green */
            color: #ffffff; /* White text for contrast */
            font-family: 'Open Sans', Arial, sans-serif; /* Clean and professional font */
            font-size: 1.2em; /* Slightly larger font for emphasis */
            font-weight: 600; /* Bold for readability */
            padding: 20px; /* Add padding for spacing */
            border-radius: 10px; /* Rounded corners for a modern look */
            text-align: center; /* Center the text */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add a slight shadow for depth */
            margin: 20px auto; /* Center alignment and spacing around the message */
            max-width: 800px; /* Limit the width for better focus */
        }

        .diagnostic-success a {
            color: #ffffff; /* Match link color with text */
            text-decoration: underline; /* Underline links to make them stand out */
            font-weight: bold; /* Emphasize links */
        }

        .diagnostic-success a:hover {
            color: #FFD700; /* Add a hover effect for interaction feedback */
        }

        .status-message {
            background: linear-gradient(90deg, #FFD700, #FFA500); /* Warm gradient for progress */
            color: #000; /* Dark text for contrast */
            font-family: 'Roboto', Arial, sans-serif; /* Clean font */
            font-size: 1.2em;
            font-weight: 600;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            margin: 20px auto;
            max-width: 800px;
            animation: fadeIn 1s ease-in-out; /* Smooth fade-in effect */
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
    @php
        $user = auth()->user();
        $activeDiagnostic=Diagnostic::getActiveDiagnostic($user->id);

//        dump($user->getActiveDiagnostic());
    @endphp

    @if($user->isAdmin() && (!$activeDiagnostic || $activeDiagnostic->progress<1))
        <x-metronic-card title="Admin Area">
            {{--            @dump($user->diagnosticCount())--}}
            {{--            @dump($user->getActiveDiagnostic())--}}
            <form action="{{ route('form-entry.load.in-progress', ['user' => $user->id]) }}" method="GET" style="width:100%;display:inline;text-align:center;">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    Load Form Data
                </button>
            </form>
        </x-metronic-card>
    @endif

    {{-- Hide diagnostic dash widget if the user has completed the diagnostic over 10 minutes ago. --}}
    @if ($action !== 'completed' || ($diagnostic->end_date->isPast() && now()->diffInMinutes($diagnostic->end_date, true) < 10))
        @include('diagnostic.dash-widget', ['diagnostic' => $diagnostic, 'action' => $action])
    @endif

    @include('files.dash-widget')
    @include('tasks.dash-widget')
    @include('notes.dash-widget')
    {{--    @if($diagnostic && $diagnostic->isCompleted())--}}
    {{--        @include('diagnostic.pdf')--}}
    {{--    @endif--}}
</x-app-layout>
