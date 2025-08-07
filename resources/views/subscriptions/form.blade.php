    <div class="container my-5" style="max-width: 600px;">
        <h1 class="text-center">Subscribe to Your Plan</h1>
        <form action="{{ route('subscription.process') }}" method="POST" id="payment-form">
            @csrf
            <input type="hidden" name="stripe_product_price_id" value="{{ $stripeProductPriceId }}">

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="business_name" class="form-label">Business Name</label>
                <input type="text" id="business_name" name="business_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="coupon" class="form-label">Coupon Code (Optional)</label>
                <input type="text" id="coupon" name="coupon" class="form-control">
            </div>

            <div class="mb-3">
                <label for="card-holder-name" class="form-label">Cardholder Name</label>
                <input type="text" id="card-holder-name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="card-element" class="form-label">Credit or Debit Card</label>
                <div id="card-element" class="form-control"></div>
            </div>

            <input type="hidden" id="payment-method" name="payment_method">

            <button id="card-button" class="btn btn-primary w-100" type="submit">
                Complete Subscription
            </button>
        </form>
    </div>

    <!-- Include Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        // Hide postal code
        const cardElement = elements.create('card', {
            hidePostalCode: true
        });

        cardElement.mount('#card-element');

        const cardButton = document.getElementById('card-button');
        const form = document.getElementById('payment-form');
        const paymentMethodInput = document.getElementById('payment-method');

        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: document.getElementById('card-holder-name').value,
                },
            });

            if (error) {
                alert(error.message);
            } else {
                paymentMethodInput.value = paymentMethod.id;
                form.submit();
            }
        });
    </script>