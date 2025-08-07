{{--<div class="mb-5 mb-xl-10" id="security_view">--}}

{{--<div class="container-fluid">--}}
<div class="card card-flush shadow-sm">
    <div class="card-body">
        <!-- Update Password -->
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="mb-10">
                @livewire('profile.update-password-form')
            </div>
        @endif

        <!-- Two-Factor Authentication -->
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="mb-10">
                @livewire('profile.two-factor-authentication-form')
            </div>
        @endif

        <!-- Logout Other Browser Sessions -->
        <div class="mb-10">
            @livewire('profile.logout-other-browser-sessions-form')
        </div>

        <!-- Delete User -->
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <div class="mb-10">
                @livewire('profile.delete-user-form')
            </div>
        @endif

    </div>
</div>
{{--</div>--}}
