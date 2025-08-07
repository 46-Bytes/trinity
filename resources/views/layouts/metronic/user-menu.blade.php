<div class="d-flex align-items-center ms-1" id="kt_header_user_menu_toggle">
    <!--begin::User info-->
    <div class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 px-2 px-md-3"
         data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
        <!--begin::Name-->
        <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2 me-md-4">
            <span class="text-muted fs-8 fw-semibold lh-1 mb-1">{{ Auth::user()->name }}</span>
        </div>
        <!--end::Name-->
        <!--begin::Symbol-->
        <div class="symbol symbol-30px symbol-md-40px">
            <img src="{{ Auth::user()->profile_photo_url }}" alt="image"/>
        </div>
        <!--end::Symbol-->
    </div>
    <!--end::User info-->
    <!--begin::User account menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
         data-kt-menu="true" style="">
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <div class="menu-content d-flex align-items-center px-3">
                <!--begin::Avatar-->
                <div class="symbol symbol-50px me-5">
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="image"/>
                </div>
                <!--end::Avatar-->
                <!--begin::Username-->
                <div class="d-flex flex-column">
                    <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::user()->name }}</div>
                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                </div>
                <!--end::Username-->
            </div>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu separator-->
        <div class="separator my-2"></div>
        <!--end::Menu separator-->
        <!--begin::Menu item-->
        <div class="menu-item px-5">
            <a href="/account" class="menu-link px-5">My Account</a>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu item-->
        <div class="menu-item px-5">
            <a class="menu-link px-5" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        <!--end::Menu item-->
    </div>
    <!--end::User account menu-->
</div>
