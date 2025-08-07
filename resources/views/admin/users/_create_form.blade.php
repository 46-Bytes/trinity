<form id="kt_modal_create_user_form" class="form" action="{{ route('users.store') }}" method="POST">
    @csrf
    <!-- Hidden field to indicate AJAX request -->
    <input type="hidden" name="is_ajax" value="1">

    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_create_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
         data-kt-scroll-dependencies="#kt_modal_create_user_header" data-kt-scroll-wrappers="#kt_modal_create_user_scroll" data-kt-scroll-offset="300px">

        <!-- Display validation errors -->
        <div id="validation-errors" class="alert alert-danger mb-5" style="{{ $errors->any() ? '' : 'display: none;' }}">
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-danger">Error</h4>
                <ul id="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- First Name -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">First Name</label>
            <input type="text" name="first_name" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('first_name') ? 'is-invalid' : '' }}" placeholder="First name"
                   value="{{ old('first_name') }}" required/>
            @error('first_name')
            <div class="invalid-feedback" id="first_name-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Last Name</label>
            <input type="text" name="last_name" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('last_name') ? 'is-invalid' : '' }}" placeholder="Last name"
                   value="{{ old('last_name') }}" required/>
            @error('last_name')
            <div class="invalid-feedback" id="last_name-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Email</label>
            <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="example@domain.com"
                   value="{{ old('email') }}" required/>
            @error('email')
            <div class="invalid-feedback" id="email-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Password</label>
            <input type="password" name="password" class="form-control form-control-solid mb-3 mb-lg-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Password (minimum 8 characters)"
                   required minlength="8"/>
            <div class="text-muted fs-7 mt-1">Password must be at least 8 characters long.</div>
            @error('password')
            <div class="invalid-feedback" id="password-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Confirm Password" required minlength="8"/>
        </div>

        <!-- Role -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Role</label>
            <select name="role" class="form-select form-select-solid {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                <option value="">Select a role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="advisor" {{ old('role') == 'advisor' ? 'selected' : '' }}>Advisor</option>
                <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
            </select>
            @error('role')
            <div class="invalid-feedback" id="role-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Organization -->
        <div class="fv-row mb-7">
            <label class="required fw-bold fs-6 mb-2">Organization</label>
            <div class="d-flex flex-column">

                <!-- New Organization Fields -->
                <div id="new_org_fields" class="ps-5 mb-5">
                    <div class="mb-3">
                        <label class="required fw-semibold fs-6 mb-2">Organization Name</label>
                        <input type="text" name="org_name" class="form-control form-control-solid {{ $errors->has('org_name') ? 'is-invalid' : '' }}" placeholder="Organization name"
                               value="{{ old('org_name') }}"/>
                        @error('org_name')
                        <div class="invalid-feedback" id="org_name-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold fs-6 mb-2">ABN</label>
                        <input type="text" name="abn" class="form-control form-control-solid {{ $errors->has('abn') ? 'is-invalid' : '' }}" placeholder="Australian Business Number"
                               value="{{ old('abn') }}"/>
                        @error('abn')
                        <div class="invalid-feedback" id="abn-error">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the organization's Australian Business Number (optional)</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold fs-6 mb-2">Website</label>
                        <input type="url" name="website" class="form-control form-control-solid {{ $errors->has('website') ? 'is-invalid' : '' }}" placeholder="https://example.com"
                               value="{{ old('website') }}"/>
                        @error('website')
                        <div class="invalid-feedback" id="website-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center pt-15">
        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
        <button type="submit" class="btn btn-primary" id="kt_modal_create_user_submit" data-kt-users-modal-action="submit">
            <span class="indicator-label">Submit</span>
            <span class="indicator-progress">Please wait... 
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </div>
</form>
