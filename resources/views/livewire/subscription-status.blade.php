<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Only show the buttons if the subscription is not canceled -->
    @if($subscription->stripe_status !== 'canceled')
        <div class="d-flex justify-content-end align-items-center gap-3 pb-0 px-0">
            @if($subscription->onGracePeriod())
                <button wire:click="resume" class="btn btn-success">
                    <i class="fa-solid fa-play"></i> Resume
                </button>
            @else
                <button wire:click="pause" class="btn btn-warning">
                    <i class="fa-solid fa-pause"></i> Pause
                </button>
            @endif

            <button wire:click="cancel" class="btn btn-danger">
                <i class="fa-solid fa-stop"></i> Cancel
            </button>
        </div>
    @endif
</div>
