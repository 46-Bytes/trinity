<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fs-2hx fw-bold text-gray-800 me-2 mb-0">Users</h1>
        <x-metronic-modal 
            buttonName="<i class='fas fa-plus'></i> Create User"
            modalHeaderText="Create New User"
            modalView="admin.users._create_form"
            dataBsTarget="kt_modal_create_user"
        />
    </div>
    
    <x-metronic-card>
        <table id="users_table" class="table table-row-bordered table-row-dashed gy-4 align-middle">
            <thead>
                <tr class="fw-bold text-muted bg-light">
                    <th class="ps-4 min-w-200px">Name</th>
                    <th class="min-w-200px">Email</th>
                    <th class="min-w-150px">Roles</th>
                    <th class="min-w-150px">Organization</th>
                    <th class="min-w-100px text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="ps-4">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->roles->isNotEmpty())
                                {{ $user->roles->pluck('name')->join(', ') }}
                            @else
                                <span class="text-muted">No roles assigned</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($user->org->name) && !empty($user->org->name))
                                {{ $user->org->name ?? 'No organization' }}
                            @else
                                <span class="text-muted">No organization</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-icon btn-sm btn-light-primary me-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
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
    </x-metronic-card>
</x-app-layout>

@push('scripts')
<script>
    // Custom function to show Toastr messages
    function showToastr(type, message) {
        // Make sure toastr is available
        if (typeof toastr !== 'undefined') {
            switch(type) {
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

    $(document).ready(function() {
        // Check for flash messages and display with Toastr
        @if(session('success'))
            showToastr('success', "{{ session('success') }}");
        @endif
        
        @if(session('error'))
            showToastr('error', "{{ session('error') }}");
        @endif
        
        // Initialize DataTable
        $('#users_table').DataTable({
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

        // Client-side validation for the password field
        $('input[name="password"]').on('input', function() {
            const password = $(this).val();
            if (password.length < 8) {
                $(this).addClass('is-invalid');
                if (!$('#password-client-error').length) {
                    $(this).after('<div id="password-client-error" class="invalid-feedback">Password must be at least 8 characters long.</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $('#password-client-error').remove();
            }
        });

        // Handle form submission via AJAX
        $('#kt_modal_create_user_form').on('submit', function(e) {
            e.preventDefault();
            console.log('Form submission started');
            
            // Perform client-side validation
            let hasErrors = false;
            
            // Validate password length
            const password = $('input[name="password"]').val();
            if (password.length < 8) {
                $('input[name="password"]').addClass('is-invalid');
                if (!$('#password-client-error').length) {
                    $('input[name="password"]').after('<div id="password-client-error" class="invalid-feedback">Password must be at least 8 characters long.</div>');
                }
                hasErrors = true;
            }
            
            // If there are client-side validation errors, don't submit the form
            if (hasErrors) {
                showToastr('error', 'Please fix the validation errors before submitting.');
                return false;
            }
            
            // Reset previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('#validation-errors').hide();
            $('#error-list').empty();
            
            // Show loading indicator
            const submitBtn = $('#kt_modal_create_user_submit');
            submitBtn.attr('data-kt-indicator', 'on');
            submitBtn.prop('disabled', true);
            
            // Get form data
            const form = $(this);
            const formData = new FormData(form[0]);
            
            // Log form data for debugging
            console.log('Form action:', form.attr('action'));
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Submit form via AJAX
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Success response:', response);
                    
                    // Show success message with Toastr
                    showToastr('success', response.message || "User created successfully!");
                    
                    // Hide modal and reload page on success
                    $('#kt_modal_create_user').modal('hide');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.log('Error status:', status);
                    console.log('Error:', error);
                    console.log('Response:', xhr.responseText);
                    
                    // Reset button state
                    submitBtn.removeAttr('data-kt-indicator');
                    submitBtn.prop('disabled', false);
                    
                    // Handle validation errors
                    if (xhr.status === 422) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.errors) {
                                // Show validation errors with Toastr
                                $.each(response.errors, function(key, value) {
                                    if (Array.isArray(value)) {
                                        $.each(value, function(i, message) {
                                            showToastr('error', message);
                                            
                                            // Also add to the error list in the modal
                                            $('#error-list').append('<li>' + message + '</li>');
                                        });
                                    } else {
                                        showToastr('error', value);
                                        $('#error-list').append('<li>' + value + '</li>');
                                    }
                                    
                                    // Highlight the invalid field
                                    $('[name="' + key + '"]').addClass('is-invalid');
                                    $('#' + key + '-error').text(Array.isArray(value) ? value[0] : value);
                                });
                                
                                // Show error container in the modal
                                $('#validation-errors').show();
                                
                                // Scroll to the top of the modal to show the error message
                                $('#kt_modal_create_user_scroll').animate({ scrollTop: 0 }, 'slow');
                            } else {
                                showToastr('error', 'Validation failed. Please check your inputs.');
                                $('#error-list').append('<li>Validation failed. Please check your inputs.</li>');
                                $('#validation-errors').show();
                            }
                        } catch (e) {
                            showToastr('error', 'Validation failed. Please check your inputs.');
                            $('#error-list').append('<li>Validation failed. Please check your inputs.</li>');
                            $('#validation-errors').show();
                        }
                    } else {
                        // Handle other errors
                        let errorMessage = 'An unexpected error occurred. Please try again.';
                        
                        // Try to extract error message from response
                        try {
                            const responseJson = JSON.parse(xhr.responseText);
                            if (responseJson.error) {
                                errorMessage = responseJson.error;
                            } else if (responseJson.message) {
                                errorMessage = responseJson.message;
                            }
                        } catch (e) {
                            // If we can't parse the JSON, use the error text
                            if (error) {
                                errorMessage = error;
                            }
                        }
                        
                        // Show error with Toastr
                        showToastr('error', errorMessage);
                        
                        // Also add to the modal error list
                        $('#error-list').append('<li>' + errorMessage + '</li>');
                        $('#validation-errors').show();
                    }
                }
            });
        });
        
        // Reset form when modal is closed
        $('#kt_modal_create_user').on('hidden.bs.modal', function() {
            // Only reset if there are no validation errors
            if (!$('#validation-errors').is(':visible')) {
                $('#kt_modal_create_user_form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('#validation-errors').hide();
                $('#error-list').empty();
                $('#password-client-error').remove();
            }
            
            // Reset the loading state of the button
            $('#kt_modal_create_user_submit').removeAttr('data-kt-indicator');
            $('#kt_modal_create_user_submit').prop('disabled', false);
        });
        
        // Check if there are any validation errors and open the modal if needed
        @if ($errors->any())
        $('#kt_modal_create_user').modal('show');
        
        // Also show errors with Toastr
        @foreach ($errors->all() as $error)
            showToastr('error', "{{ $error }}");
        @endforeach
        @endif
    });
</script>
@endpush
