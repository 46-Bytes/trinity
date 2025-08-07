@php
    use App\Models\Product;
@endphp

        <!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <title>yoba - Your Online Business Advisor</title>
    <meta charset="utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="article"/>
    <meta property="og:title" content="yoba - Your Online Business Advisor"/>
    <meta property="og:url" content="https://yoba.app"/>
    <meta property="og:site_name" content="yoba by MyMalekso"/>
    <link rel="canonical" href="https://yoba.app"/>
    <link rel="shortcut icon" href="{{asset('favicon.png')}}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <!-- Video.js CSS -->
    <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />

    <!-- Video.js JavaScript -->
    <script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>
    <!-- Scripts -->
    @include('layouts.public.header-scripts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
        if (window.top !== window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
    <script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" data-bs-spy="scroll" data-bs-target="#kt_landing_menu" class="bg-body position-relative">
<!--begin::Theme mode setup on page load-->
<script>
    var defaultThemeMode = "light";
    var themeMode;
    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }</script>
<!--end::Theme mode setup on page load-->
<!--begin::Main-->
<!--begin::Root-->
<div class="container d-flex flex-column flex-root" style="box-shadow: 0px 0px 10px #ebebeb; padding: 0px !important;">
    <!--begin::Header Section-->
    <div class="mb-0" id="home">
        <!--begin::Wrapper-->
        <div class=" mb-10 mb-lg-20 yoba-section-bgi-header box-shadow-inset">
            <div class=""></div>
            <!--begin::Header-->
            <div class="landing-header" data-kt-sticky="true" data-kt-sticky-name="landing-header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
                <!--begin::Container-->
                <div class="container yoba-section-content">
                    <!--begin::Wrapper-->
                    <div class="d-flex align-items-center justify-content-between">
                        <!--begin::Logo-->
                        <div class="d-flex align-items-center flex-equal">
                            <!--begin::Mobile menu toggle-->
                            <button class="btn btn-icon btn-active-color-primary me-3 d-flex d-lg-none" id="kt_landing_menu_toggle">
                                <i class="ki-duotone ki-abstract-14 fs-2hx">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                            <!--end::Mobile menu toggle-->
                            <!--begin::Logo image-->
                            <!-- Logo -->
                            <a href="/">
                                <img alt="Logo" src="{{ asset('logo.png') }}" class="h-80px h-lg-80px ms-lg-5 ms-0"/>
                            </a>
                            {{--                            <a href="/">--}}
                            {{--                                <img alt="Logo" src="{{ asset('metronic/media/logos/landing.svg') }}" class="logo-default h-25px h-lg-30px"/>--}}
                            {{--                                <img alt="Logo" src="{{ asset('metronic/media/logos/landing-dark.svg') }}" class="logo-sticky h-20px h-lg-25px"/>--}}
                            {{--                            </a>--}}
                            <!--end::Logo image-->
                        </div>
                        <!--end::Logo-->
                        <!--begin::Menu wrapper-->
                        <div class="d-lg-block" id="kt_header_nav_wrapper">
                            <div class="d-lg-block p-5 p-lg-0" data-kt-drawer="true" data-kt-drawer-name="landing-menu" data-kt-drawer-activate="{default: true, lg: false}"
                                 data-kt-drawer-overlay="true" data-kt-drawer-width="200px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_landing_menu_toggle" data-kt-swapper="true"
                                 data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
                                <!--begin::Menu-->
                                <div class="menu menu-column flex-nowrap menu-rounded menu-lg-row menu-title-gray-600 menu-state-title-primary nav nav-flush fs-5 fw-semibold" id="kt_landing_menu">
                                    <!--begin::Menu item-->
                                    <div class="menu-item">
                                        <!--begin::Menu link-->
                                        <a class="menu-link nav-link active py-3 px-4 px-xxl-6" style="color:black;" href="#kt_body" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Home</a>
                                        <!--end::Menu link-->
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item">
                                        <!--begin::Menu link-->
                                        <a class="menu-link nav-link py-3 px-4 px-xxl-6" style="color:black;" href="#how-it-works" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">How it
                                            Works</a>
                                        <!--end::Menu link-->
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item">
                                        <!--begin::Menu link-->
                                        <a class="menu-link nav-link py-3 px-4 px-xxl-6" style="color:black;" href="#register" data-kt-scroll-toggle="true"
                                           data-kt-drawer-dismiss="true">Register Now</a>
                                        <!--end::Menu link-->
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    {{--                                    <div class="menu-item">--}}
                                    {{--                                        <!--begin::Menu link-->--}}
                                    {{--                                        <a class="menu-link nav-link py-3 px-4 px-xxl-6" style="color:black;" href="#team" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Team</a>--}}
                                    {{--                                        <!--end::Menu link-->--}}
                                    {{--                                    </div>--}}
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item">
                                        <!--begin::Menu link-->
                                        <a class="menu-link nav-link py-3 px-4 px-xxl-6" style="color:black;" href="#pricing" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Pricing</a>
                                        <!--end::Menu link-->
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->
                            </div>
                        </div>
                        <!--end::Menu wrapper-->
                        <!--begin::Toolbar-->
                        @if (Route::has('login'))
                            <div class="flex-equal text-end ms-1">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none
                                    focus-visible:ring-[#FF2D20]">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-success"> Sign In</a>

                                    {{--                                    @if (Route::has('register'))--}}
                                    {{--                                        <a href="{{ route('register') }}" class="btn btn-primary">Register </a>--}}
                                    {{--                                    @endif--}}
                                @endauth
                            </div>
                        @endif
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Header-->
            <!--begin::Landing hero-->
            <div class="d-flex flex-column flex-center w-100 min-h-350px min-h-lg-500px px-9">
                <!--begin::Heading-->
                <div class="text-center mb-5 mb-lg-10 py-10 py-lg-20" style="max-width: 850px;">
                    {{--                    <!--begin::Youtube-->--}}
                    {{--                    <a class="d-block rounded min-h-100px" style="" data-fslightbox="lightbox-youtube" href="https://www.youtube.com/watch?v=C0DPdy98e4c">--}}
                    {{--                        <!--begin::Icon-->--}}
                    {{--                        <img src="{{ asset('img/video-icon.png') }}" class="text-center mb-5" alt="" width="100px" style="margin: 0 auto;"/>--}}
                    {{--                        <!--end::Icon-->--}}
                    {{--                    </a>--}}
                    {{--                    <!--end::Youtube-->--}}
                    <x-video-player
                            path="{{asset('videos/yoba_home_intro.mp4')}}"
                            type="video/mp4"
                            id="vid-home-intro"
                            width="690"
                            height="388"
                            :autoplay="false"
                            :muted="false"
                            poster="{{asset('videos/yoba_home_intro_poster.jpg')}}"
                    />
                </div>
                <!--end::Heading-->
                <div style="text-align:center;font-weight:500;margin-bottom: 90px;">
                    <h3 style="color:darkorange;" class="fs-2hx fw-bold bann-heading">Run your business like a pro with the power of AI</h3>
                    <p style="margin-bottom: 15px;"><strong>24/7 Business advice. Practical tools. Real results.</strong><br>Stuck? Growing? Just starting? We’ve got you covered.</p>
                    <p style="margin-bottom: 15px;">Whether you’re stuck in a rut, aiming for the next big milestone, or starting out,<br><span class="yoba-orange-bold">Yoba</span> is your secret
                        weapon to take your business to the next level.</p>
                    <p>For just <strong>$11/month</strong>, <span class="yoba-orange-bold">Yoba</span> combines the expertise of seasoned business advisors with cutting-edge AI to deliver
                        actionable insights,<br> personalised support, and tools to supercharge your business – <strong>anytime, anywhere</strong>.</p>
                </div>
            </div>
            <!--end::Landing hero-->

        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Header Section-->
    <!--begin::How It Works Section-->
    <div class="mb-n10 mb-lg-n20 z-index-2">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Heading-->
            <div class="text-center mb-17">
                <!--begin::Title-->
                <h3 class="fs-2hx yoba-orange mb-5" id="how-it-works" data-kt-scroll-offset="{default: 100, lg: 150}">How Yoba Works</h3>
                <!--end::Title-->
                <!--begin::Text-->
                <div class="fs-2 text-black fw-bold">AI-Driven Business Support in 3 Simple Steps:
                </div>
                <!--end::Text-->
            </div>
            <!--end::Heading-->
            <!--begin::Row-->
            <div class="row w-100 gy-10 mb-md-20">
                <!--begin::Col-->
                <div class="col-md-4 px-5">
                    <!--begin::Story-->
                    <div class="text-center mb-10 mb-md-0">
                        <!--begin::Illustration-->
                        <div class="d-flex flex-center"><img src="{{ asset('metronic/media/illustrations/sketchy-1/2-dark.png') }}" class="mh-125px mb-9" alt=""/></div>
                        <!--end::Illustration-->
                        <!--begin::Heading-->
                        <div class="d-flex flex-center mb-5">
                            <!--begin::Badge-->
                            <span class="badge badge-circle badge-light-success fw-bold p-5 me-3 fs-3">1</span>
                            <!--end::Badge-->
                            <!--begin::Title-->
                            <div class="fs-5 fs-lg-3 fw-bold text-gray-900">Take The Diagnostic</div>
                            <!--end::Title-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Description-->
                        <div class="fw-semibold fs-6 fs-lg-4 text-black">Share key details about your business. <span class="yoba-orange-bold">Yoba’s</span> AI analyses your input to generate tailored
                            insights.
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Story-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-4 px-5">
                    <!--begin::Story-->
                    <div class="text-center mb-10 mb-md-0">
                        <!--begin::Illustration-->
                        <div class="d-flex flex-center"><img src="{{ asset('metronic/media/illustrations/sketchy-1/12-dark.png') }}" class="mh-125px mb-9" alt=""/></div>
                        <!--end::Illustration-->
                        <!--begin::Heading-->
                        <div class="d-flex flex-center mb-5">
                            <!--begin::Badge-->
                            <span class="badge badge-circle badge-light-success fw-bold p-5 me-3 fs-3">2</span>
                            <!--end::Badge-->
                            <!--begin::Title-->
                            <div class="fs-5 fs-lg-3 fw-bold text-gray-900">Get instant feedback</div>
                            <!--end::Title-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Description-->
                        <div class="fw-semibold fs-6 fs-lg-4 text-black">Receive a personalised report packed with strategies, action plans, and recommended tasks.
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Story-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-4 px-5">
                    <!--begin::Story-->
                    <div class="text-center mb-10 mb-md-20">
                        <!--begin::Illustration-->
                        <div class="d-flex flex-center"><img src="{{ asset('metronic/media/illustrations/sketchy-1/7-dark.png') }}" class="mh-125px mb-9" alt=""/></div>
                        <!--end::Illustration-->
                        <!--begin::Heading-->
                        <div class="d-flex flex-center mb-5">
                            <!--begin::Badge-->
                            <span class="badge badge-circle badge-light-success fw-bold p-5 me-3 fs-3">3</span>
                            <!--end::Badge-->
                            <!--begin::Title-->
                            <div class="fs-5 fs-lg-3 fw-bold text-gray-900">Start growing</div>
                            <!--end::Title-->
                        </div>
                        <!--end::Heading-->
                        <!--begin::Description-->
                        <div class="fw-semibold fs-6 fs-lg-4 text-black">Use <span class="yoba-orange-bold">Yoba</span> every day via the chat with <span class="yoba-orange-bold">Yoba</span> feature
                            to dive deeper into advice, set priorities, and get help staying on track. Whatever your questions, <span class="yoba-orange-bold">Yoba</span> can help.
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Story-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--end::Container-->
        </div>
    </div>
    <!--end::How It Works Section-->
    <!--begin::Register Now Section-->
    <div class="mt-sm-n10" style="margin-bottom: 30px;">
        <!--begin::Statistics-->
        <div class="yoba-section-bgi-register box-shadow-inset"><!--begin::Container-->

            <div class="container yoba-section-content">
                <!--begin::Heading-->
                <div class="text-center mt-15 mb-18 pt-18" id="register" data-kt-scroll-offset="{default: 100, lg: 150}">
                    <!--begin::Title-->
                    <h3 class="fs-2hx yoba-orange fw-bold mb-5" style="max-width:900px;margin:0 auto;">Personal & professional business advice for just $11 a month. That’s about the price of 2 coffees
                        a month…</h3>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="fs-2 text-gray-700 fw-bold">Because AI-Powered Advice Changes Everything.</div>
                    <!--end::Description-->
                </div>
                <!--end::Heading-->
                <!--begin::Statistics-->
                <div class="d-flex flex-center">
                    <!--begin::Items-->
                    <div class="d-flex flex-wrap flex-center justify-content-lg-between mb-15 mx-auto w-xl-900px">
                        <!--begin::Item-->
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="text-center" style="margin-top: 250px;">
                                    <h3 class="fs-2hx yoba-orange fw-bold mb-5">Just think about that for a minute!</h3>
                                    <span class="fs-2 fw-bold">Isn’t the future of your business worth 2 cups of coffee?</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Item-->
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Statistics-->
                <!--begin::Register-->
                <div class="fs-2 fw-semibold text-muted text-center pb-15">
                    <a href="#pricing" class="btn btn-primary bg-color-orange mb-10">No Contracts • No Risks • Just Results</a>
                </div>
                <!--end::Register-->
            </div>
            <!--end::Container--><!--begin::Container-->

        </div>
        <!--end::Statistics-->
    </div>
    <!--end::Register Now Section-->
    <!--begin::Pricing Section-->
    <div class="mt-sm-n10">
        <!--begin::Statistics-->
        <div class="pb-15 pt-18 yoba-section-bgi-need-clarity box-shadow-inset"><!--begin::Container-->

            <div class="container yoba-section-content">
                <!--begin::Heading-->
                <div class="text-center mt-15 mb-18" id="register" data-kt-scroll-offset="{default: 100, lg: 150}">
                    <!--begin::Title-->
                    <h3 class="fs-2hx yoba-orange fw-bold mb-5">Need clarity?</h3>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="fs-2 text-gray-700 fw-bold">
                        <span><span class="yoba-orange-bold">Yoba's</span> AI explains everything — from budgeting basics to cashflow management, business planning, and more.<br>Talk to <span
                                    class="yoba-orange-bold">Yoba</span> about anything related to your business for immediate, accurate advice and guidance.</span>
                    </div>
                    <!--end::Description-->
                </div>
                <!--end::Heading-->
                <!--begin::NeedClarity-->
                <div class="d-flex flex-center">
                    <!--begin::Items-->
                    <div class="d-flex flex-wrap flex-center justify-content-lg-between mb-15 mx-auto w-xl-1000px">
                        @php $opacity = 'opacity-50' @endphp
                        <div class="container my-5">
                            <div class="row g-3">
                                <!-- Image 1 -->
                                <div class="col-6 col-md-3">
                                    <div class="card shadow-sm">
                                        <img src="/img/man-barista.jpg" class="card-img-top rounded img-fluid bw {{$opacity}}" alt="Image 1">
                                    </div>
                                </div>
                                <!-- Image 2 -->
                                <div class="col-6 col-md-3">
                                    <div class="card shadow-sm">
                                        <img src="/img/combine-harvester.jpg" class="card-img-top rounded img-fluid bw {{$opacity}}" alt="Image 2">
                                    </div>
                                </div>
                                <!-- Image 3 -->
                                <div class="col-6 col-md-3">
                                    <div class="card shadow-sm">
                                        <img src="/img/men-women-company-team.jpg" class="card-img-top rounded img-fluid bw {{$opacity}}" alt="Image 3">
                                    </div>
                                </div>
                                <!-- Image 4 -->
                                <div class="col-6 col-md-3">
                                    <div class="card shadow-sm">
                                        <img src="/img/woman-holding-open-sign.jpg" class="card-img-top rounded img-fluid bw {{$opacity}}" alt="Image 4">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--begin::Item-->
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-12 text-center" style="margin-top:30px;">
                                    <!--begin::Title-->
                                    <h3 class="fs-2hx yoba-orange fw-bold mb-5">Who should be using Yoba?</h3>
                                    <!--end::Title-->
                                </div>
                                <div class="col-md-12 text-center ">
                                    <!--begin::Description-->
                                    <div class="fs-2 text-gray-700 fw-bold">
                                        <p>Start-ups. SMEs. Sole traders. Partnerships. Companies. Any industry. Anywhere in the world.</p>
                                        <p><strong>If you have a business, <span class="yoba-orange-bold">Yoba's</span> AI is your secret weapon.</strong></p>
                                        <p>And all for the price of just two cups of coffee a month!</p>
                                    </div>
                                    <!--end::Description-->
                                </div>
                                <div class="col-md-12 text-center mt-10">
                                    <!--begin::Title-->
                                    <h3 class="fs-2hx yoba-orange fw-bold mb-5">What’s in it for you?</h3>
                                    <!--end::Title-->
                                </div>
                                <div class="col-md-12">
                                    <ul class="list-unstyled">
                                        <li class="mb-10"><strong>Instant Clarity:</strong> An AI-powered diagnostic report, tailored to your business, with advice you can act on today.</li>
                                        <li class="mb-10"><strong>Ongoing Support:</strong> AI tracks your progress and keeps you focused on your goals.</li>
                                        <li class="mb-10"><strong>Expert Knowledge:</strong> yoba’s AI combines decades of real-world business expertise with real-time insights.</li>
                                        <li class="mb-10"><strong>Valuable Tools:</strong> Resources to build a more profitable, sustainable business.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--end::Item-->
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Statistics-->
            </div>
            <!--end::Container-->

        </div>
        <!--end::Statistics-->
        <div class="yoba-section-bgi-why-choose box-shadow-inset"><!--begin::Container-->

            <!--end::Container-->
            <!--begin::Container-->
            <div class="container yoba-section-content">
                <!--begin::Heading-->
                <div class="text-center mt-15 mb-18 pt-18" id="register" data-kt-scroll-offset="{default: 100, lg: 150}">
                    <!--begin::Title-->
                    <h3 class="fs-2hx yoba-orange fw-bold mb-5">Why choose Yoba?</h3>
                    <!--end::Title-->
                    <!--begin::Description-->
                    <div class="fs-2 text-gray-700 fw-bold">Because AI-Powered Advice Changes Everything.</div>
                    <!--end::Description-->
                </div>
                <!--end::Heading-->
                <!--begin::Statistics-->
                <div class="d-flex flex-center">
                    <!--begin::Items-->
                    <div class="d-flex flex-wrap flex-center justify-content-lg-between mb-15 mx-auto w-xl-1000px">
                        <!--begin::Item-->
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="">
                                    <ul class="list-unstyled">
                                        <li class="mb-10"><strong>Affordable:</strong> Professional, AI-enhanced business insights for the price of a couple of cups of coffee a month.</li>
                                        <li class="mb-10"><strong>Accessible:</strong> On your laptop, tablet, or phone — 24/7. Anytime. Anywhere.</li>
                                        <li class="mb-10"><strong>Actionable:</strong> Clear, practical steps to help you move forward.</li>
                                        <li class="mb-10"><strong>Tailored:</strong> Custom advice that’s as unique as your business.</li>
                                        <li class="mb-10"><strong>Smarter:</strong> Leverage AI to analyse, diagnose, and track your business performance.</li>
                                        <li class="mb-10"><strong>No Strings:</strong> No lock-in contracts, no hidden fees.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--end::Item-->
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Statistics-->
                <!--begin::Register-->
                <div class="fs-2 fw-semibold text-muted text-center pb-10">
                    <a href="#pricing" class="btn btn-primary">Start Today!</a>
                </div>
                <!--end::Register-->
            </div>
            <!--end::Container-->

        </div>
        <!--end::Statistics-->
    </div>
    <!--end::Register Now Section-->
    <!--begin::Pricing Section-->
    <div id="pricing" class="mt-sm-n20 yoba-section-pricing box-shadow-inset" style="margin: 40px 0px !important;">

        <div class="pt-40 pb-40">
            <!-- begin::Container -->
            <div class="container text-center mb-15">

                <!--begin::Title-->
                <h1 class="fs-2hx yoba-orange fw-bold mb-5">
                    Clear Pricing That Makes Sense
                </h1>
                <!--end::Title-->

                <p>Simple pricing with no hidden fees.</p>

            </div>
            <div class="container text-center">
                <div class="row" style="border:3px solid #F27200;display: flex;align-items: center;">
                    <div class="col-md-2 px-5 pt-10 pb-10">
                        <p><span class="yoba-orange fs-2" style="font-weight:900;font-size: 50px !important; line-height: 36px;">BASIC</span></p>
                    </div>
                    <div class="col-md-8 px-5 pt-10 pb-10">
                        <p><span class="fs-2" style="font-weight:600; line-height: 36px;">Start with Basic today. Includes AI powered Business Diagnostic, tailored recommendations, live chat with yoba and much more. </span>
                        </p>
                    </div>
                    <div class="col-md-2 px-5 pt-10 pb-10">
                        <p><span class="yoba-orange fs-2" style="font-weight:900;font-size: 50px !important; line-height: 50px;">$11 MONTH</span></p>
                    </div>
                </div>

                <div class="fs-2 fw-semibold text-muted text-center pb-10" style="margin-top: -20px;">

                    <form method="GET" action="{{ route('register') }}">
                        <input type="hidden" name="stripe_product_price_id" value="{{Product::getStripePriceIdBySlug('basic')}}">
                        {{--                        <input type="hidden" name="stripe_product_price_id" value="price_1QROCXPcm1KjIvUYj7e5nMlt">--}}
                        <button type="submit" class="btn btn-primary">SELECT</button>
                    </form>

                </div>

                <div class="row" style="align-items: center;">
                    <div class="col-md-2 px-5 pt-10 pb-10">

                    </div>
                    <div class="col-md-8 px-5 pt-10 pb-10">
                        <img src="/img/coming-soon-img.png" style="margin: 0 0 0 -50px;"/>
                        <p><span class="fs-1" style="font-weight:700;">Advanced & Premium Plans are coming soon, with everything in the Basic Plan plus file upload, external advisor collaboration, AI enhanced tools for deeper insights and much more. Watch this space! </span>
                        </p>
                    </div>
                    <div class="col-md-2 px-5 pt-10 pb-10">

                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--end::Pricing Section-->
    <!--begin::Footer Section-->
    <div class="mb-0 mt-sm-n15 yoba-section-bgi-why-wait box-shadow-inset" style="margin-top: 10px !important;">

        <!--begin::Wrapper-->
        <div class="pt-40">
            <!-- begin::Container -->
            <div class="container text-center mb-15">

                <!--begin::Title-->
                <h1 class="fs-2hx yoba-orange fw-bold mb-5">
                    Why Wait? Let's get started!
                </h1>
                <!--end::Title-->

                <p class="fs-2">If you’re ready to stop worrying and start thriving, sign up today and discover the smarter,
                    AI-powered way to run your business.</p>

            </div>
            <div class="container text-center">
                <div class="row">
                    <div class="col-md-8 px-5"></div>
                    <div class="col-md-4 px-5">
                        <h3 class="yoba-orange fs-2hx fw-bold" style="">Smart, simple, effective & ridiculously affordable.</h3>
                        <p><span class="fs-5"><strong style="font-size: 30px !important; line-height: 36px;">Register now and let Yoba take your business to the next level!</strong></span></p>
                        <a href="{{ route('dashboard') }}">
                            <img alt="Logo" src="{{ asset('yoba-hellix-logo-withsub.png') }}" class="mt-15 mb-15 ms-0"/>
                        </a>
                        <div class="fs-2 fw-semibold text-muted text-center pb-10">
                            <a href="#pricing" class="btn btn-primary bg-color-orange">No Contracts • No Risks • Just Results</a>
                        </div>
                        <div class="fs-2 fw-semibold text-muted text-center pb-15">
                            <a href="#pricing" class="btn btn-primary">Sign Up Today & and start growing!</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Register-->

            <!--end::Register-->
            <!-- end::Container -->
            <!--begin::Separator-->
            <div class="landing-dark-separator"></div>
            <!--end::Separator-->
            <!--begin::Container-->
            <div class="container">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column flex-md-row flex-stack py-7 py-lg-10">
                    <!--begin::Copyright-->
                    <div class="d-flex align-items-center order-2 order-md-1">
                        <!--begin::Logo-->

                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}">
                            <img alt="Logo" src="{{ asset('logo.png') }}" class="h-50px h-lg-60px ms-lg-5 ms-0"/>
                        </a>
                        <!--end::Logo image-->
                        <!--begin::footer copyright-->
                        <span class="mx-5 fs-6 fw-semibold text-gray-600 pt-1" href="https://mymalekso.com.au">&copy; {{ date('Y') }} MyMalekso</span>
                        <!--end::footer copyright-->
                    </div>
                    <!--end::Copyright-->
                    <!--begin::Menu-->
                    {{--                    <ul class="menu menu-gray-600 menu-hover-primary fw-semibold fs-6 fs-md-5 order-1 mb-5 mb-md-0">--}}
                    {{--                        <li class="menu-item">--}}
                    {{--                            <a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="menu-item mx-5">--}}
                    {{--                            <a href="https://devs.keenthemes.com" target="_blank" class="menu-link px-2">Support</a>--}}
                    {{--                        </li>--}}
                    {{--                        <li class="menu-item">--}}
                    {{--                            <a href="" target="_blank" class="menu-link px-2">Purchase</a>--}}
                    {{--                        </li>--}}
                    {{--                    </ul>--}}
                    <!--end::Menu-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Footer Section-->
    <!--begin::Scrolltop-->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>
    <!--end::Scrolltop-->
</div>
<!--end::Root-->
<!--end::Main-->
<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</div>
<!--end::Scrolltop-->
<!--begin::Scripts-->
@include('layouts.public.footer-scripts')
<!--end::Scripts-->
</body>
<!--end::Body-->
</html>
