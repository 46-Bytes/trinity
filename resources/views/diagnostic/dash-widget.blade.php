@php use App\Models\Diagnostic; @endphp
{{--@dump($action)--}}
<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Diagnostic Progress</span>
        </h3>
        <div class="card-toolbar">
            @if ($action === 'continue')
                <!-- Continue Button if Diagnostic is in Progress -->
                <a href="{{ route('diagnostic.show', $diagnostic->id) }}" class="btn btn-primary mt-3">
                    <i class="fa fa-arrow-right"></i>
                    Continue Diagnostic
                </a>
            @elseif ($action === 'start_monthly')
                <!-- Start Monthly Diagnostic Button -->
                <a href="{{ route('diagnostic.create') }}" class="btn btn-success mt-3">
                    <i class="fa fa-play"></i>
                    Start Monthly Diagnostic
                </a>
            @elseif ($action === 'start_primary')
                <!-- Start Diagnostic Button -->
                <a href="{{ route('diagnostic.create') }}" class="btn btn-success mt-3">
                    <i class="fa fa-play"></i>
                    Start Diagnostic
                </a>
            @elseif ($action === 'completed')
                <!-- Download Diagnostic Button -->
                <a href="{{ route('files.downloadLatestDiagnostic') }}" class="btn btn-yoba-orange mt-3" style="color:white;">
                    <i class="fa fa-download fa-lg" style="color:white;font-size:20px;"></i>
                    Download Diagnostic Summary
                </a>
            @endif
        </div>
    </div>

    <div class="card-body pt-9 pb-0 mb-10">
        {{--        @if ($diagnostic)--}}
        {{--            @dump($diagnostic->debug())--}}
        {{--        @endif--}}
        @if ($diagnostic && !$diagnostic->isCompleted())
            <p>Your diagnostic is still in progress.</p>
            <div class="progress" style="width:100%">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $diagnostic->progress }}%;"
                     aria-valuenow="{{ $diagnostic->progress }}"
                     aria-valuemin="0" aria-valuemax="100">{{ $diagnostic->progress }}%
                </div>
            </div>
        @elseif ($diagnostic && $diagnostic->isCompleted())
            @if($diagnostic->advice)
                <p>Your report is now complete – please download and review.</p>
                <p>Don’t forget, come back and chat with Trinity anytime about this business.</p>

                <div style="text-align: center;margin-top:20px;"><a href="/chat" class="btn btn-success font-bold">Chat with Trinity Now</a></div>
            @else
                <div id="statusMessage" class="status-message">
                    {!! TRINITYAI !!} is currently reviewing your diagnostic data. Please hold on while we prepare your personalized
                    advice.
                </div>

                <script type="text/javascript">
                    function checkDiagnosticStatus(diagnosticId) {
                        fetch(`/api/diagnostic-status/${diagnosticId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.adviceReady) {
                                    location.reload(); // Refresh the page
                                } else {
                                    console.log("Advice not ready yet. Retrying...");
                                }
                            })
                            .catch(error => console.error("Error fetching diagnostic status:", error));
                    }

                    // Start polling with diagnostic ID
                    const diagnosticId = {{ $diagnostic->id ?? 'null' }};
                    if (diagnosticId) {
                        const pollingInterval = setInterval(() => {
                            checkDiagnosticStatus(diagnosticId);
                        }, 5000);
                    }
                </script>

            @endif
        @else
            <p>You haven't started a diagnostic yet.</p>
        @endif
    </div>
</div>
