<div class="mb-5 mb-xl-10" id="overview_view">
    <!-- Update Profile Information -->
    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <div class="mb-10">
            @livewire('profile.update-profile-information-form')
        </div>
    @endif

    <x-metronic-card title="Business Details">
        @if($org)
            <form method="POST" action="{{ route('orgs.update', $org->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $org->name) ?? $org->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description', $org->description) ?? $org->description }}" required>
                </div>

                <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="text" id="website" name="website" class="form-control" value="{{ old('website', $org->website) ?? $org->website }}" required>
                </div>

                <div class="mb-3">
                    <label for="address_line1" class="form-label">Address - Line 1</Address></label>
                    <input type="text" id="address_line1" name="address_line1" class="form-control" value="{{ old('address_line1', $org->address_line1) ?? $org->address_line1 }}">
                </div>

                <div class="mb-3">
                    <label for="address_line2" class="form-label">Address - Line 2</label>
                    <input type="text" id="address_line2" name="address_line2" class="form-control" value="{{ old('address_line2', $org->address_line2) ?? $org->address_line2 }}">
                </div>

                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" value="{{ old('city', $org->city) ?? $org->city }}">
                </div>

                <div class="mb-3">
                    <label for="state" class="form-label">State</label>
                    <input type="text" id="state" name="state" class="form-control" value="{{ old('state', $org->state) ?? $org->state }}">
                </div>

                <div class="mb-3">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code', $org->postal_code) ?? $org->postal_code }}">
                </div>

                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" name="country" class="form-control" value="{{ old('country', $org->country) ?? $org->country }}">
                </div>

                <div class="mt-20 text-end">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        @else
            <p>No business details found</p>
        @endif
    </x-metronic-card>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
