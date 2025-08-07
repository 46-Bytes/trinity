<x-public-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Pricing Card -->
            <div class="card" id="kt_pricing">
                <!-- Card Body -->
                <div class="card-body p-lg-17">
                    <!-- Plans -->
                    <div class="d-flex flex-column">
                        <!-- Heading -->
                        <div class="mb-13 text-center">
                            <h1 class="fs-2hx fw-bold mb-5">Choose Your Plan</h1>
                            <div class="text-gray-600 fw-semibold fs-5">
                                Select the best plan for your needs or <a href="#" class="link-primary fw-bold">learn more</a>.
                            </div>
                        </div>
                        <!-- Dynamic Row -->
                        <div class="row g-10">
                            @foreach ($plans as $plan)
                                @php
                                    // Dynamic column sizing based on number of plans
                                    $colClass = count($plans) === 1 ? 'col-xl-12' : (count($plans) === 2 ? 'col-xl-6' : 'col-xl-4');
                                @endphp
                                        <!-- Dynamic Col -->
                                <div class="{{ $colClass }}">
                                    <div class="d-flex h-100 align-items-center">
                                        <!-- Plan Option -->
                                        <div
                                                class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10">
                                            <!-- Heading -->
                                            <div class="mb-7 text-center">
                                                <h1 class="text-gray-900 mb-5 fw-bolder">{{ $plan['name'] }}</h1>
                                                <div class="text-gray-600 fw-semibold mb-5">
                                                    {{ $plan['description'] ?? 'No description available' }}
                                                </div>
                                                <div class="text-center">
                                                    <span class="mb-2 text-primary">$</span>
                                                    <span class="fs-3x fw-bold text-primary">{{ $plan['amount'] }}</span>
                                                    <span class="fs-7 fw-semibold opacity-50">/ {{ $plan['currency'] }}</span>
                                                </div>
                                            </div>
                                            <!-- Select Button -->
                                            <form action="{{ route('plans.subscribe') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="plan" value="{{ $plan['stripe_id'] }}">
                                                <button type="submit" class="btn btn-sm btn-primary">Select</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
