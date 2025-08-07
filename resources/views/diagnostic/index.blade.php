@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp
<x-app-layout>
    <div class="d-flex flex-column flex-lg-row h-100">
        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10 d-flex flex-column h-100">
            <div class="card">
                <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
                    <div class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Diagnostic Reports</span>
                        <span class="text-muted mt-1 fs-7">View and manage your diagnostic reports</span>
                    </div>
                    @if($user->diagnosticCount() === 0)
                        <a href="{{ route('diagnostic.create') }}" class="btn btn-success">
                            Start Diagnostic
                        </a>
                    @endif
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_diagnostics">
                            <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Start Date</th>
                                <th class="min-w-100px">End Date</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px">Download Report</th>
                                <th class="text-end min-w-100px">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                            @forelse($diagnostics as $diagnostic)
                                <tr>
                                    <td>{{ $diagnostic->start_date ? $diagnostic->start_date->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td>{{ $diagnostic->end_date ? $diagnostic->end_date->format('M d, Y H:i') : 'In Progress' }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                App\Models\Diagnostic::STATUS_NEEDS_ACTION => 'badge-light-warning',
                                                App\Models\Diagnostic::STATUS_IN_PROGRESS => 'badge-light-primary',
                                                App\Models\Diagnostic::STATUS_COMPLETED => 'badge-light-success'
                                            ][$diagnostic->status] ?? 'badge-light-dark';

                                            $statusLabel = ucfirst(str_replace('-', ' ', $diagnostic->status));
                                        @endphp
                                        <span class="badge {{ $statusClass }} fs-7 fw-bold">{{ $statusLabel }}</span>
                                    </td>
                                    <td>
                                        @if($diagnostic->isCompleted() && $diagnostic->end_date)
                                            <a href="{{ $diagnostic->getDownloadUrl() }}" class="text-primary fw-bold">
                                                {{ $diagnostic->getDownloadFilename() }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not available</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if(in_array($diagnostic->status, [App\Models\Diagnostic::STATUS_NEEDS_ACTION, App\Models\Diagnostic::STATUS_IN_PROGRESS]))
                                                <a href="{{ route('diagnostic.show', $diagnostic->id) }}" class="btn btn-sm btn-primary">
                                                    Continue
                                                </a>
                                            @endif

                                            @if($diagnostic->status === App\Models\Diagnostic::STATUS_COMPLETED && !$user->hasIncompleteDiagnostic())
                                                <a href="{{ route('diagnostic.clone', $diagnostic->id) }}" class="btn btn-sm btn-light" data-kt-diagnostic-id="{{ $diagnostic->id }}">
                                                    Clone
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-10">
                                        No diagnostic reports found. Start by creating a new diagnostic.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>