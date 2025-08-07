<div class="card mb-5 mb-xxl-8">
    <!--begin::Header-->
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Notes</span>
            <span class="fs-7 fw-semibold text-muted">Showing {{ $notes->count() }} notes</span>
        </h3>
        <div class="card-toolbar">
            <x-metronic-modal buttonName='<i class="fa fa-solid fa-sticky-note"></i> New Note'
                              modalHeaderText="New Note"
                              modalView="notes.modal-dash-create" dataBsTarget="kt_modal_create_note"/>
        </div>
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body pt-9 pb-0 mb-0">

        @foreach ($notes as $note)
            <!--begin::Item-->
            <div class="d-flex align-items-center" style="margin-bottom:20px;">
                <!--begin::Bullet-->
                <span class="bullet bullet-vertical h-40px mx-3" style="background-color:{{ $note->color }}"></span>
                <!--end::Bullet-->
                <!--begin::Description-->
                <div class="flex-grow-1">
                    <a href="/notes?note_id={{$note->id}}" class="text-gray-800 text-hover-primary fw-bold fs-6"> {{ $note->title }}</a>
                    <span class="text-muted fw-semibold d-block"> {{ Str::limit($note->content, 100) }}</span>
                </div>
                <!--end::Description-->
                @php
                    $noteDate = $note->updated_at;
                    $badgeClass = 'badge-light-danger'; // Default to red if more than a week
                    if ($noteDate->diffInDays(now()) <= 2) {
                        $badgeClass = 'badge-light-success'; // Green if within 2 days
                    } elseif ($noteDate->diffInDays(now()) > 2 && $noteDate->diffInDays(now()) < 7) {
                        $badgeClass = 'badge-light-warning'; // Orange if between 2 days and a week
                    }
                @endphp

                <span class="badge {{ $badgeClass }} fs-8 fw-bold">{{ $noteDate->diffForHumans() }}</span>

            </div>
            <!--end:Item-->
        @endforeach

    </div>
    <!--end::Body-->
</div>
