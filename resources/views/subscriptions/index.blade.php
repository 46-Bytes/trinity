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
                    <h1 class="text-2xl font-bold mb-6">Your Active Subscriptions</h1>

                    @if ($subscriptions->isEmpty())
                        <p>You currently have no active subscriptions.</p>
                        <a href="/#pricing"
                           class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Available Plans
                        </a>
                    @else
                        @foreach ($subscriptions as $subscription)
                            <div class="mb-6 p-4 border rounded">
                                <h2 class="text-xl font-semibold">{{ $subscription->name }}</h2>
                                <p class="text-gray-600">
                                    {{ $subscription->stripe_price ? 'Price ID: ' . $subscription->stripe_price : '' }}
                                </p>
                                <p>Status:
                                    <span class="{{ $subscription->active() ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $subscription->active() ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                                <p>Ends at:
                                    {{ $subscription->ends_at ? $subscription->ends_at->format('F j, Y') : 'N/A' }}
                                </p>

                                @if ($subscription->onGracePeriod())
                                    <form action="{{ route('subscription.resume') }}" method="POST"
                                          class="inline-block mt-4">
                                        @csrf
                                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                                        <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Resume Subscription
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('subscription.cancel') }}" method="POST"
                                          class="inline-block mt-4">
                                        @csrf
                                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                                        <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Cancel Subscription
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
