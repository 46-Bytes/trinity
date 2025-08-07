@props([
    'buttonName' => null,
    'modalHeaderText' => null,
    'modalView' => null,
    'dataBsTarget' => null,
    'viewVars' => []
    ])

<!-- Actions -->
<div class="d-flex align-items-center py-2 py-md-1">
    <button type="button" class="btn btn-primary fw-bold" id="kt_toolbar_primary_button" data-bs-toggle="modal"
            data-bs-target="#{{ $dataBsTarget }}">
        {!! $buttonName !!}
    </button>
</div>

<div class="modal fade" id="{{ $dataBsTarget }}" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <h2 class="fw-bold">{{ $modalHeaderText }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y">

                <!-- Include the modal view if provided -->
                @includeIf($modalView,['viewVars' => $viewVars])

                <!-- Modal Action Buttons -->
                {{--                <div class="d-flex justify-content-end">--}}
                {{--                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>--}}
                {{--                </div>--}}

            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
