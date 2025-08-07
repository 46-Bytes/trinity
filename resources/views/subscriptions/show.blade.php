<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($subscription && $subscription->active())
                        <h1 class="text-2xl font-bold">Active Subscription</h1>
                        <p class="mt-2">Plan: {{ $subscription->name }}</p>
                        <p>Status: <span class="text-green-600 font-semibold">Active</span></p>
                        <form action="{{ route('subscription.cancel') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Cancel Subscription
                            </button>
                        </form>
                    @elseif ($subscription && $subscription->onGracePeriod())
                        <h1 class="text-2xl font-bold">Subscription in Grace Period</h1>
                        <p class="mt-2">Plan: {{ $subscription->name }}</p>
                        <p>Status: <span class="text-yellow-600 font-semibold">Grace Period</span></p>
                        <form action="{{ route('subscription.resume') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Resume Subscription
                            </button>
                        </form>
                    @else
                        <h1 class="text-2xl font-bold">No Active Subscription</h1>
                        <p class="mt-2">It seems you do not have an active subscription. Explore our plans below.</p>
                        <a href="/#pricing"
                           class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Plans
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
