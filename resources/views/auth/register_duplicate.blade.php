<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo/>
        </x-slot>

        <x-validation-errors class="mb-4"/>

        <form id="payment-form" method="POST" action="{{ route('register') }}">
            @csrf
            <!-- Hidden input for the price_id -->
            
            <div class="mt-4">
                <x-label for="first_name" value="{{ __('First Name') }}"/>
                <x-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus/>
            </div>
            <div class="mt-4">
                <x-label for="last_name" value="{{ __('Last Name') }}"/>
                <x-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autofocus/>
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}"/>
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username"/>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}"/>
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password"/>
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}"/>
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password"/>
            </div>
            <div class="mt-4">
                <x-label for="business_name" value="{{ __('Business Name') }}"/>
                <x-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" required autofocus/>
            </div>
            <div class="mt-4">
                <x-label for="business_description" value="{{ __('What does your business do?') }}"/>
                <x-input-textarea id="business_description" class="block mt-1 w-full" type="text" name="business_description" :value="old('business_description')" required autofocus/>
            </div>
            <div class="mt-4">
                <x-label for="business_website" value="{{ __('Business Website') }}"/>
                <x-input id="business_website" class="block mt-1 w-full" type="text" name="business_website" :value="old('business_website')" required autofocus/>
            </div>


            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required/>
                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button id="submit-button" class="btn btn-primary ms-4 px-5 py-3">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    <!-- Stripe Script -->

    <script>
        // Initialize Stripe

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitButton.disabled = true;

            
            // Append payment method to form data
            const formData = new FormData(form);
            

            // Submit the form via AJAX
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        cardErrors.textContent = data.error;
                    } else {
                        // alert('Registration and subscription successful!');
                        // Redirect to dashboard
                        window.location.href = data.redirect || '/dashboard';
                    }
                    submitButton.disabled = false;
                })
                
        });
        

    </script>
</x-guest-layout>
