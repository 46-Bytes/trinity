<!-- Header Component -->
<div id="kt_header" class="header">
    <div class="container-fluid d-flex flex-stack">
        <div class="d-flex align-items-center me-5">
            <!-- Aside toggle -->
            <div class="d-lg-none btn btn-icon btn-active-color-white w-30px h-30px ms-n2 me-3" id="kt_aside_toggle">
                <i class="ki-duotone ki-abstract-14 fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <!-- Logo -->
            <a href="{{ route('dashboard') }}">
                <img alt="Logo" src="{{ asset('logo.png') }}" class="h-20px h-lg-40px ms-lg-5 ms-0"/>
            </a>


            <!-- begin: Company Switcher -->
            @include('layouts.metronic.company-switcher')
            <!-- end: Company Switcher -->
        </div>

        <!-- Topbar -->
        <div class="d-flex align-items-center flex-shrink-0">
            <div class="d-flex align-items-center ms-1">
                <!-- Theme Mode Toggle -->
                <a href="#" class="btn btn-icon btn-color-white bg-hover-opacity-10 w-30px h-30px"
                   data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-night-day theme-light-show fs-1"></i>
                    <i class="ki-duotone ki-moon theme-dark-show fs-1"></i>
                </a>
                <div class="menu menu-sub menu-sub-dropdown menu-column">
                    <!-- Theme options -->
                    <div class="menu-item">
                        <a href="#" class="menu-link">Light</a>
                    </div>
                    <div class="menu-item">
                        <a href="#" class="menu-link">Dark</a>
                    </div>
                </div>
            </div>
            <!-- begin: User Menu -->
            @include('layouts.metronic.user-menu')
            <!-- end: User Menu -->
            {{--            <div class="d-flex align-items-center ms-1" id="kt_header_user_menu_toggle">--}}
            {{--                <div class="btn btn-flex align-items-center">--}}
            {{--                    <div class="symbol symbol-30px symbol-md-40px">--}}
            {{--                        <img src="{{ asset('metronic/media/avatars/300-1.jpg') }}" alt="image"/>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--                <div class="menu menu-sub menu-sub-dropdown menu-column">--}}
            {{--                    <!-- User Menu -->--}}
            {{--                    <div class="menu-item">--}}
            {{--                        <a href="account/overview.html" class="menu-link">My Profile</a>--}}
            {{--                    </div>--}}
            {{--                    <div class="menu-item">--}}
            {{--                        <a href="authentication/layouts/corporate/sign-in.html" class="menu-link">Sign Out</a>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
        </div>
    </div>
</div>
