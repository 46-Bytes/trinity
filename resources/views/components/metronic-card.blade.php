<div class="card card-flush shadow-sm mb-10">
    @if ($title)
    <div class="card-header">
        <h3 class="card-title">{{$title}}</h3>
        {{-- <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light">
                Action
            </button>
        </div> --}}
    </div>
    @endif
    <div class="card-body py-5">
        {{$slot}}
    </div>
    @if($footer)
    <div class="card-footer">
        {{$footer}}
    </div>
    @endif
</div>
