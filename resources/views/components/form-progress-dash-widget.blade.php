<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Form Progress</span>
        </h3>
        <div class="card-toolbar">
            @if($formEntry && $formEntry->percentage_complete < 100)
                <!-- Continue Button if FormEntry exists -->
                <a href="{{ route('forms.show', $formEntry->form_id) }}" class="btn btn-primary mt-3">
                    <i class="fa fa-arrow-right"></i>
                    Continue
                </a>
            @else
                <!-- Start Button if no FormEntry exists -->
                <a href="{{ route('forms.show', $formId) }}" class="btn btn-success mt-3">
                    <i class="fa fa-play"></i>
                    Start Form
                </a>
            @endif
        </div>
    </div>

    <div class="card-body pt-9 pb-0 mb-10">
        @if($formEntry && $formEntry->percentage_complete < 100)
            <p>Your form is still in progress.</p>
            <div class="progress" style="width:100%">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $formEntry->percentage_complete }}%;"
                     aria-valuenow="{{ $formEntry->percentage_complete }}"
                     aria-valuemin="0" aria-valuemax="100">{{ $formEntry->percentage_complete }}%
                </div>
            </div>
        @else
            <p>You haven't started your form yet.</p>
        @endif
    </div>
</div>
