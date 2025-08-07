<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Form Progress</span>
        </h3>
        <div class="card-toolbar">
            <a href="/forms/1" class="btn btn-primary mt-3">
                <i class="fa fa-arrow-right"></i>
                Continue
            </a>
        </div>
    </div>
    <div class="card-body pt-9 pb-0 mb-10">
        <p>Your form is still in progress.</p>
        <div class="progress" style="width:100%">
            <div class="progress-bar bg-success" role="progressbar"
                 style="width: {{ $incompleteFormEntry->percentage_complete }}%;"
                 aria-valuenow="{{ $incompleteFormEntry->percentage_complete }}"
                 aria-valuemin="0" aria-valuemax="100">{{ $incompleteFormEntry->percentage_complete }}%
            </div>
        </div>
    </div>
</div>
