<!-- Sidebar (Aside) Component -->
<div id="kt_aside" class="aside card" data-kt-drawer="true" data-kt-drawer-name="aside"
     data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
     data-kt-drawer-toggle="#kt_aside_toggle">
    <div class="aside-menu flex-column-fluid px-4" id="kt_aside_menu" data-kt-menu="true">
        <div class="hover-scroll-overlay-y mh-100 my-5" id="kt_aside_menu_wrapper">
            <div class="menu menu-column menu-rounded">
                <!-- Menu items -->
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        {{--<span class="menu-heading fw-bold text-uppercase fs-7">Menu</span>--}}
                    </div>
                </div>

                @include('menu')

                {{--                <x-yoba-side-nav/>--}}
            </div>
        </div>
    </div>

    <!-- Sidebar Footer -->
    {{--    <div class="aside-footer flex-column-auto pt-5 pb-7 px-7">--}}
    {{--        <a href="#" class="btn btn-bg-light btn-color-gray-500 disabled-link" aria-disabled="true">--}}
    {{--            <span class="btn-label  pe-2">Help</span>--}}
    {{--            <i class="fa-solid fa-circle-question" style="color: #B197FC;"></i> <!-- Purple for Help -->--}}
    {{--        </a>--}}
    {{--    </div>--}}
</div>
