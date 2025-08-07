<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Subscriptions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-6">Complete checkout</h1>

                    <form id="payment-form" method="POST" action="{{ route('checkout') }}">
                        @csrf
                        <!-- Hidden input for the price_id -->
                        <input type="hidden" name="stripe_product_price_id" value="{{ $stripe_product_price_id }}">
            
                        <div class="mt-4">
                            <div class="block font-medium text-sm text-gray-700">Subscription Plan</div>
                            <div id="price-display"
                                 data-product-name="{{ $productName }}"
                                 data-original-price="{{ $productPrice }}">
                                {{ $productName }} {{ $productPrice }} / Month
                            </div>
                            <input type="hidden" id="price_id" value="{{ $stripe_product_price_id }}">
                        </div>
                        <div id="coupon-error" class="text-red-500 text-sm mt-1"></div>
            
                        <div class="mt-4">
                            <x-label for="coupon" value="{{ __('Coupon Code (Optional)') }}"/>
                            <x-input id="coupon" class="block mt-1 w-full" type="text" name="coupon"/>
                            <span id="coupon-error" class="text-red-500 text-sm mt-1"></span>
                        </div>
            
                        <!-- Payment Section -->
                        <div class="mt-4" max-width="400px">
                            <x-label for="card-element" value="{{ __('Credit or Debit Card') }}"/>
                            <div id="card-element" class="border border-gray-300 p-2 rounded"></div>
                            <div id="card-errors" class="mt-2 text-red-500 text-sm"></div>
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
            
                            <x-button id="submit-button" class="ms-4">
                                {{ __('Subscribe') }}
                            </x-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<!-- Stripe Script -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe
    const stripe = Stripe("{{ env('STRIPE_KEY') }}"); // Replace with your Stripe public key
    const elements = stripe.elements();
    // Hide postal code
    const cardElement = elements.create('card', {
            hidePostalCode: true
        });
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const cardErrors = document.getElementById('card-errors');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submitButton.disabled = true;

        // Create PaymentMethod using Stripe.js
        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            cardErrors.textContent = error.message;
            submitButton.disabled = false;
            return;
        }

        // Append payment method to form data
        const formData = new FormData(form);
        formData.append('payment_method', paymentMethod.id);

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
                    window.location.href = 'account#billing-tab';//data.redirect || '/dashboard';
                }
                submitButton.disabled = false;
            })
            .catch(error => {
                cardErrors.textContent = 'Something went wrong. Please try again.';
                submitButton.disabled = false;
            });
    });
    document.getElementById('coupon').addEventListener('blur', function () {
        const coupon = this.value;
        const priceId = document.getElementById('price_id').value;

        if (coupon) {
            fetch('/validate-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({coupon, price_id: priceId}),
            })
                .then(response => response.json())
                .then(data => {
                    const priceDisplay = document.getElementById('price-display');
                    const errorMessage = document.getElementById('coupon-error');
                    if (data.error) {
                        errorMessage.textContent = data.error;
                        priceDisplay.textContent = ''; // Clear the display
                        document.getElementById('submit-button').disabled = true;
                    } else {
                        errorMessage.textContent = ''; // Clear error
                        priceDisplay.textContent = `Basic ${data.discounted_price.toFixed(2)} ${data.currency} / Month`;
                        document.getElementById('submit-button').disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    });

</script>