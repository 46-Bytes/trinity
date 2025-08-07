<x-app-layout>
    <div class="content flex-column-fluid" id="kt_content">
        <!--begin::Navbar-->
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <!--begin: Pic-->
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-90px symbol-fixed position-relative">
                            <img src="{{ Auth::user()->profile_photo_url ?? 'assets/media/avatars/default-avatar.jpg' }}" alt="Profile Image">
                        </div>
                    </div>
                    <!--end::Pic-->
                    <!--begin::Info-->
                    <div class="flex-grow-1">
                        <!--begin::Title-->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <!--begin::User-->
                            <div class="d-flex flex-column">
                                <!--begin::Name-->
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ Auth::user()->name }}</a>
                                </div>
                                <!--end::Name-->
                                <!--begin::Info-->
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <a href="{{ $org->website??'#' }}" target="_blank" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>{{ $org->name??'Unknown' }}</a>
                                    <div class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                        <i class="ki-duotone ki-geolocation fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>{{ $org->location??'Unknown' }}</div>
                                    <div class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                        <i class="ki-duotone ki-sms fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>{{ Auth::user()->email }}</div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::User-->
                        </div>
                        <!--end::Title-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Details-->
                <!--begin::Navs-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" id="account-tabs">
                    <!-- Dynamically create tabs -->
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#overview-tab">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#security-tab">Security</a>
                    </li>
                    {{--                    <li class="nav-item">--}}
                    {{--                        <a class="nav-link" data-bs-toggle="tab" href="#billing-tab">Billing</a>--}}
                    {{--                    </li>--}}
                    {{-- <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#logs-tab">Logs</a>
                    </li> --}}
                </ul>
                <!--end::Navs-->
            </div>
        </div>
        <!--end::Navbar-->

        <!--begin::Body-->
        <div class="tab-content mt-10">
            <!-- Overview -->
            <div class="tab-pane fade show active" id="overview-tab">
                @include('account._overview')
            </div>
            <!-- Security -->
            <div class="tab-pane fade" id="security-tab">
                @include('account._security')
            </div>
            <!-- Billing -->
            {{--            <div class="tab-pane fade" id="billing-tab">--}}
            {{--                @include('account._billing', compact('user','subscription', 'invoices','availableProducts'))--}}
            {{--            </div>--}}
            <!-- Logs -->
            <div class="tab-pane fade" id="logs-tab">
                @include('account._logs')
            </div>
        </div>
        <!--end::Body-->

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const url = new URL(window.location.href);
            const activeTab = url.hash || "#overview-tab"; // Default to Overview if no hash exists

            // Show the active tab
            const tabElement = document.querySelector(`a[href="${activeTab}"]`);
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }

            // Update the URL hash when a tab is clicked
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener("shown.bs.tab", function (event) {
                    history.pushState(null, null, event.target.getAttribute("href"));
                });
            });
        });

    </script>
</x-app-layout>
