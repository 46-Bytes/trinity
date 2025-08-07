<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fs-2hx fw-bold text-gray-800 me-2 mb-0">Organizations</h1>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_org">
            <i class="fas fa-plus"></i> Add Organization
        </button>
    </div>

    <x-metronic-card>
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="orgs_table">
                <thead>
                <tr class="fw-bolder text-muted">
                    <th class="min-w-150px">Organization</th>
                    <th class="min-w-140px">User</th>
                    <th class="min-w-120px">Status</th>
                    <th class="min-w-120px">Location</th>
                    <th class="min-w-120px">Website</th>
                    <th class="min-w-100px">Date Joined</th>
                    <th class="min-w-100px text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orgs as $org)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">

                                <div class="d-flex justify-content-start flex-column">
                                    <a href="{{ route('orgs.show', $org->id) }}" class="text-dark fw-bolder text-hover-primary fs-6">{{ $org->name }}</a>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ $org->description ?? 'No description' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($org->user)
                                <a href="{{ route('users.show', $org->user->id) }}" class="text-dark fw-bolder text-hover-primary d-block fs-6">
                                    {{ $org->user->first_name }} {{ $org->user->last_name }}
                                </a>
                                <span class="text-muted fw-bold text-muted d-block fs-7">{{ $org->user->email }}</span>
                            @else
                                <span class="text-muted fw-bold d-block fs-7">No user assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($org->status == 'active')
                                <span class="badge badge-light-success">Active</span>
                            @elseif($org->status == 'inactive')
                                <span class="badge badge-light-warning">Inactive</span>
                            @else
                                <span class="badge badge-light-danger">{{ ucfirst($org->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @if($org->city && $org->state)
                                    <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $org->city }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ $org->state }}, {{ $org->country }}</span>
                                @elseif($org->state)
                                    <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $org->state }}, {{ $org->country }}</span>
                                @elseif($org->country)
                                    <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $org->country }}</span>
                                @else
                                    <span class="text-muted fw-bold d-block fs-7">No location data</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($org->website)
                                <a href="{{ $org->website }}" target="_blank" class="text-primary fw-bold fs-6 text-hover-primary">
                                    {{ str_replace(['https://', 'http://'], '', $org->website) }}
                                    <i class="fas fa-external-link-alt fs-8 ms-1"></i>
                                </a>
                            @else
                                <span class="text-muted fw-bold d-block fs-7">No website</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark fw-bolder d-block fs-6">{{ $org->date_joined ? date('M d, Y', strtotime($org->date_joined)) : 'N/A' }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('orgs.edit', $org->id) }}" class="btn btn-icon btn-sm btn-light-primary me-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('orgs.destroy', $org->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this organization?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-light-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-metronic-card>

    <!-- Create Organization Modal -->
    <div class="modal fade" id="kt_modal_create_org" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_create_org_header">
                    <h2 class="fw-bolder">Add Organization</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <i class="fas fa-times"></i>
                        </span>
                    </div>
                </div>

                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_create_org_form" class="form" action="{{ route('orgs.store') }}" method="POST">
                        @csrf

                        <!-- Display validation errors -->
                        <div id="validation-errors" class="alert alert-danger mb-5" style="display: none;">
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">Error</h4>
                                <ul id="error-list"></ul>
                            </div>
                        </div>

                        <!-- Organization Name -->
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Organization Name</label>
                            <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Enter organization name" required/>
                        </div>

                        <!-- Description -->
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Description</label>
                            <textarea name="description" class="form-control form-control-solid" rows="3" placeholder="Brief description of the organization"></textarea>
                        </div>

                        <!-- Website -->
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Website</label>
                            <input type="url" name="website" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="https://example.com"/>
                        </div>

                        <!-- Address Line 1 -->
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Address Line 1</label>
                            <input type="text" name="address_line1" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Street address"/>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Address Line 2</label>
                            <input type="text" name="address_line2" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Suite, unit, building, etc."/>
                        </div>

                        <!-- City, State, Postal Code -->
                        <div class="row mb-7">
                            <div class="col-md-4">
                                <div class="fv-row">
                                    <label class="fw-bold fs-6 mb-2">City</label>
                                    <input type="text" name="city" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="City"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row">
                                    <label class="fw-bold fs-6 mb-2">State</label>
                                    <input type="text" name="state" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="State/Province"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row">
                                    <label class="fw-bold fs-6 mb-2">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Postal code"/>
                                </div>
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Country</label>
                            <input type="text" name="country" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Country"/>
                        </div>

                        <!-- User Association -->
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Associated User</label>
                            <select name="user_id" class="form-select form-select-solid">
                                <option value="">Select a user (optional)</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Select a user to associate with this organization (optional)</div>
                        </div>

                        <!-- Status -->
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Status</label>
                            <select name="status" class="form-select form-select-solid" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="text-center pt-15">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="kt_modal_create_org_submit">
                                <span class="indicator-label">Create Organization</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        // Custom function to show Toastr messages
        function showToastr(type, message) {
            // Make sure toastr is available
            if (typeof toastr !== 'undefined') {
                switch (type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'error':
                        toastr.error(message);
                        break;
                    case 'warning':
                        toastr.warning(message);
                        break;
                    case 'info':
                        toastr.info(message);
                        break;
                    default:
                        console.log(message);
                }
            } else {
                console.error('Toastr is not available');
                alert(message);
            }
        }

        $(document).ready(function () {
            // Check for flash messages and display with Toastr
            @if(session('success'))
            showToastr('success', "{{ session('success') }}");
            @endif

            @if(session('error'))
            showToastr('error', "{{ session('error') }}");
            @endif

            // Initialize DataTable
            $('#orgs_table').DataTable({
                "info": true,
                "ordering": true,
                "paging": true,
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "search": "Search:",
                    "lengthMenu": "_MENU_",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });

            // Handle form submission
            $('#kt_modal_create_org_form').on('submit', function (e) {
                e.preventDefault();

                // Show loading indicator
                const submitBtn = $('#kt_modal_create_org_submit');
                submitBtn.attr('data-kt-indicator', 'on');
                submitBtn.prop('disabled', true);

                // Clear previous errors
                $('#validation-errors').hide();
                $('#error-list').empty();

                // Submit form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        // Show success message
                        showToastr('success', 'Organization created successfully');

                        // Reload the page after a short delay
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function (xhr) {
                        // Reset button state
                        submitBtn.attr('data-kt-indicator', 'off');
                        submitBtn.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorList = $('#error-list');

                            // Display each error
                            $.each(errors, function (key, value) {
                                errorList.append('<li>' + value + '</li>');
                            });

                            // Show error container
                            $('#validation-errors').show();
                        } else {
                            // General error
                            showToastr('error', 'Failed to create organization. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@endpush
