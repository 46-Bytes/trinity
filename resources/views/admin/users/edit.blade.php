<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fs-2hx fw-bold text-gray-800 me-2 mb-0">Edit User</h1>
        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-light-primary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>
    
    <x-metronic-card>
        <form action="{{ route('users.update', $user->id) }}" method="POST" id="kt_edit_user_form">
            @csrf
            @method('PUT')
            
            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_edit_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_edit_user_header" data-kt-scroll-wrappers="#kt_modal_edit_user_scroll" data-kt-scroll-offset="300px">
                
                <!-- Display validation errors -->
                <div id="validation-errors" class="alert alert-danger mb-5" style="{{ $errors->any() ? '' : 'display: none;' }}">
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Error</h4>
                        <ul id="error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <!-- First Name -->
                <div class="fv-row mb-7">
                    <label class="required fw-bold fs-6 mb-2">First Name</label>
                    <input type="text" name="first_name" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('first_name') ? 'is-invalid' : '' }}" placeholder="First name" value="{{ old('first_name', $user->first_name) }}" required/>
                    @error('first_name')
                    <div class="invalid-feedback" id="first_name-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Last Name -->
                <div class="fv-row mb-7">
                    <label class="required fw-bold fs-6 mb-2">Last Name</label>
                    <input type="text" name="last_name" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('last_name') ? 'is-invalid' : '' }}" placeholder="Last name" value="{{ old('last_name', $user->last_name) }}" required/>
                    @error('last_name')
                    <div class="invalid-feedback" id="last_name-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Email -->
                <div class="fv-row mb-7">
                    <label class="required fw-bold fs-6 mb-2">Email</label>
                    <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="example@domain.com" value="{{ old('email', $user->email) }}" required/>
                    @error('email')
                    <div class="invalid-feedback" id="email-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Password -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">Password</label>
                    <input type="password" name="password" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Leave blank to keep current password" minlength="8"/>
                    <div class="text-muted fs-7 mt-1">Password must be at least 8 characters long. Leave blank to keep current password.</div>
                    @error('password')
                    <div class="invalid-feedback" id="password-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Password Confirmation -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Confirm Password" minlength="8"/>
                </div>
                
                <!-- Role -->
                <div class="fv-row mb-7">
                    <label class="required fw-bold fs-6 mb-2">Role</label>
                    <select name="role" class="form-select form-select-solid {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role', $user->roles->first()->name ?? '') == $role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                    <div class="invalid-feedback" id="role-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Organization information -->
                <div class="separator separator-dashed my-10"></div>
                
                <h3 class="fs-1 fw-bolder mb-5">Organization Details</h3>
                
                <!-- Organization Name -->
                <div class="fv-row mb-7">
                    <label class="required fw-bold fs-6 mb-2">Organization Name</label>
                    <input type="text" name="org_name" class="form-control form-control-solid {{ $errors->has('org_name') ? 'is-invalid' : '' }}" 
                           value="{{ old('org_name', $user->org->name ?? '') }}" required/>
                    @error('org_name')
                    <div class="invalid-feedback" id="org_name-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ABN -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">ABN</label>
                    <input type="text" name="abn" class="form-control form-control-solid {{ $errors->has('abn') ? 'is-invalid' : '' }}" 
                           placeholder="Australian Business Number" value="{{ old('abn', $user->org->abn ?? '') }}"/>
                    @error('abn')
                    <div class="invalid-feedback" id="abn-error">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter the organization's Australian Business Number (optional)</div>
                </div>
                
                <!-- Website -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">Website</label>
                    <input type="url" name="website" class="form-control form-control-solid {{ $errors->has('website') ? 'is-invalid' : '' }}" 
                           placeholder="https://example.com" value="{{ old('website', $user->org->website ?? '') }}"/>
                    @error('website')
                    <div class="invalid-feedback" id="website-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Address Line 1 -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">Address Line 1</label>
                    <input type="text" name="address_line1" class="form-control form-control-solid {{ $errors->has('address_line1') ? 'is-invalid' : '' }}" 
                           placeholder="Street address" value="{{ old('address_line1', $user->org->address_line1 ?? '') }}"/>
                    @error('address_line1')
                    <div class="invalid-feedback" id="address_line1-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Address Line 2 -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">Address Line 2</label>
                    <input type="text" name="address_line2" class="form-control form-control-solid {{ $errors->has('address_line2') ? 'is-invalid' : '' }}" 
                           placeholder="Suite, unit, building, etc." value="{{ old('address_line2', $user->org->address_line2 ?? '') }}"/>
                    @error('address_line2')
                    <div class="invalid-feedback" id="address_line2-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- City, State, Postal Code -->
                <div class="row mb-7">
                    <div class="col-md-4">
                        <div class="fv-row">
                            <label class="fw-bold fs-6 mb-2">City</label>
                            <input type="text" name="city" class="form-control form-control-solid {{ $errors->has('city') ? 'is-invalid' : '' }}" 
                                   value="{{ old('city', $user->org->city ?? '') }}"/>
                            @error('city')
                            <div class="invalid-feedback" id="city-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fv-row">
                            <label class="fw-bold fs-6 mb-2">State</label>
                            <input type="text" name="state" class="form-control form-control-solid {{ $errors->has('state') ? 'is-invalid' : '' }}" 
                                   value="{{ old('state', $user->org->state ?? '') }}"/>
                            @error('state')
                            <div class="invalid-feedback" id="state-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fv-row">
                            <label class="fw-bold fs-6 mb-2">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control form-control-solid {{ $errors->has('postal_code') ? 'is-invalid' : '' }}" 
                                   value="{{ old('postal_code', $user->org->postal_code ?? '') }}"/>
                            @error('postal_code')
                            <div class="invalid-feedback" id="postal_code-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Country -->
                <div class="fv-row mb-7">
                    <label class="fw-bold fs-6 mb-2">Country</label>
                    <input type="text" name="country" class="form-control form-control-solid {{ $errors->has('country') ? 'is-invalid' : '' }}" 
                           value="{{ old('country', $user->org->country ?? '') }}"/>
                    @error('country')
                    <div class="invalid-feedback" id="country-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="text-center pt-15">
                <a href="{{ route('admin.users') }}" class="btn btn-light me-3">Cancel</a>
                <button type="submit" class="btn btn-primary" id="kt_edit_user_submit">
                    <span class="indicator-label">Save Changes</span>
                    <span class="indicator-progress">Please wait... 
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </form>
    </x-metronic-card>
</x-app-layout>

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle form submission
        $('#kt_edit_user_form').on('submit', function(e) {
            // Show loading indicator
            const submitBtn = $('#kt_edit_user_submit');
            submitBtn.attr('data-kt-indicator', 'on');
            submitBtn.prop('disabled', true);
        });
    });
</script>
@endpush
