<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold fs-2hx text-dark mb-5">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container-fluid">
        <div class="card card-flush shadow-sm mb-10">
            <div class="card-body py-10 px-10">
                <!-- Update Profile Information -->
                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    <div class="mb-10">
                        @livewire('profile.update-profile-information-form')
                    </div>
                    <div class="separator my-5"></div>
                @endif

                <!-- Update Password -->
                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                    <div class="mb-10">
                        @livewire('profile.update-password-form')
                    </div>
                    <div class="separator my-5"></div>
                @endif

                <!-- Two-Factor Authentication -->
                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <div class="mb-10">
                        @livewire('profile.two-factor-authentication-form')
                    </div>
                    <div class="separator my-5"></div>
                @endif

                <!-- Logout Other Browser Sessions -->
                <div class="mb-10">
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>

                <!-- Delete User -->
                @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                    <div class="separator my-5"></div>
                    @livewire('profile.delete-user-form')
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
