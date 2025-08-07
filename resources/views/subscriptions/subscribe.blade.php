<form id="subscribe-form" action="{{ route('subscribe') }}" method="POST">
    @csrf
    <label for="plan">Choose a Plan:</label>
    <select name="plan" id="plan" required>
        <option value="price_1Nc8RsLD4zMonthly">Monthly Plan</option>
        <option value="price_1Nc8RsLD4zYearly">Yearly Plan</option>
    </select>

    <div id="card-element"></div> <!-- Stripe Element -->
    <button type="submit">Subscribe</button>
</form>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');
    const elements = stripe.elements();
    // Hide postal code
    const cardElement = elements.create('card', {
            hidePostalCode: true
        });
    cardElement.mount('#card-element');

    const form = document.getElementById('subscribe-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            console.error(error);
            alert('Payment failed. Please try again.');
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);

            form.submit();
        }
    });
</script>
