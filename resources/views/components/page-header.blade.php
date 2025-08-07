@props(['title' => 'Page Title', 'buttonName' => null, 'modalHeaderText' => null, 'modalView' => null, 'dataBsTarget' => null,
    'viewVars' => []])

<div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
    <!-- Page Title -->
    <div class="page-title d-flex flex-column me-3">
        <h1 class="d-flex text-gray-900 fw-bold my-1 fs-3">{{ $title }}</h1>
    </div>

    @if($buttonName && $modalView)
        <x-metronic-modal buttonName="{{$buttonName}}" modalHeaderText="{{ $modalHeaderText }}"
                          modalView="{{$modalView}}" dataBsTarget="{{ $dataBsTarget }}" :viewVars="$viewVars"/>

        {{--        <!-- Actions -->--}}
        {{--        <div class="d-flex align-items-center py-2 py-md-1">--}}
        {{--            <button type="button" class="btn btn-primary fw-bold" id="kt_toolbar_primary_button" data-bs-toggle="modal"--}}
        {{--                    data-bs-target="#{{ $dataBsTarget }}">--}}
        {{--                {{ $buttonName }}--}}
        {{--            </button>--}}
        {{--        </div>--}}

        {{--        <!-- Include the modal view if provided -->--}}
        {{--        @include($modalView)--}}
    @endif
</div>
