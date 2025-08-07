<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{asset('favicon.png')}}"/>
    <!-- Video.js CSS -->
    <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />

    <!-- Video.js JavaScript -->
    <script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>
    @include('layouts.metronic.header-scripts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-head.tinymce-config/>

    <!-- Styles -->
    @livewireStyles
    <style>
        .menu-link {
            margin-bottom: 4px !important;
            padding: 0.65rem 2rem !important;
        }

        .menu-icon i {
            color: #3c98e0 !important;
        }

        .menu-item {
            font-weight: 400;
            font-size: 18px;
            line-height: 22px;
        }

        .aside-footer .btn {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        #kt_content {
            padding-left: 20px;
            padding-right: 20px;
            flex: 1 0 auto; /* Ensure content grows and takes up space */
            flex-grow: 1; /* Allows the content area to grow */
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Makes the page take the full height */
        }

        main {
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Allow the main content to take up extra space */
            justify-content: center; /* Centers the card vertically */
        }

        .card {
            margin-bottom: 0; /* Ensure no margin on the bottom of the card */
        }

        .header {
            background-color: #fff !important;
        }
    </style>
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed">
<x-banner/>
<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">
        <!-- Aside (Sidebar) -->
        @include('layouts.metronic.aside')

        <!-- Main Wrapper -->
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <!-- Header -->
            @include('layouts.metronic.header')

            <!-- Main Content -->
            <div class="content flex-column-fluid" id="kt_content">
                @if (isset($header))
                    {{--                    <x-page-header title="Dashboard" createLink="{{ route('your.create.route') }}"/>--}}
                    {{ $header }}
                @endif
                <main>

                    <x-metronic-toastr/>

                    {{ $slot }}

                </main>
            </div>

            <!-- Footer -->
            @include('layouts.metronic.footer')
        </div>
    </div>
</div>

<!-- Scroll to Top Button -->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up"></i>
</div>

@stack('modals')
@livewireScripts
<!-- Scripts -->
@include('layouts.metronic.footer-scripts')
</body>
</html>
