@php use Carbon\Carbon; @endphp
<div class="mb-5 mb-xl-10" id="billing_view">
    <div class="card  mb-5 mb-xl-10">
        <!--begin::Card body-->
        <div class="card-body">
            @if(!$subscription || $subscription->stripe_status === 'canceled')
                <!--begin::Notice-->
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-12 p-6">
                    <!--begin::Icon-->
                    <i class="ki-duotone ki-information fs-2tx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>        <!--end::Icon-->

                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack flex-grow-1 ">
                        <!--begin::Content-->
                        <div class=" fw-semibold">
                            <h4 class="text-gray-900 fw-bold">We need your attention!</h4>
                            <div class="fs-6 text-gray-700 ">Your subscription is no longer active. Please update your payment details to avoid service interruption.</div>
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Notice-->
                <form id="payment-form" method="GET" action="{{ route('checkout') }}">
                    <div class="row">
                        <div class="col-lg-6">
                            
                                <select class="form-select" id="stripe_product_price_id" name="stripe_product_price_id">
                                    @php
                                        foreach ($availableProducts as $key => $value) {
                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                        }
                                    @endphp
                                </select>
                            
                        </div>
                        <div class="col-lg-6">
                            <x-button id="submit-button" class="ms-4">
                                {{ __('Subscribe') }}
                            </x-button>
                        </div>
                    </div>
                </form>
            @else
                <!--begin::Row-->
                <div class="row">
                    <!--begin::Heading-->
                    <h3 class="mb-2">Your Subscription</h3>
                    <!--end::Heading-->
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row">
                    <!--begin::Col-->
                    <div class="col-lg-7">
                        <!--begin::Info-->
                        <div class="mb-2">{{$productName}}: <span class="text-gray-800 fw-bold me-1">{{ strtoupper($subscription->asStripeSubscription()
                            ->plan->currency) }} ${{ $subscription->asStripeSubscription()->plan->amount / 100 }} </span>
                            <span class="text-gray-600 fw-semibold">Per {{ $subscription->asStripeSubscription()->plan->interval }}</span>
                            @if ($subscription->stripe_status === 'canceled')
                                - Subscription cancelled
                            @elseif ($subscription->onGracePeriod())
                                - Grace period ends {{ Carbon::createFromTimestamp($stripeSubscription->current_period_end)->toFormattedDateString() }}
                            @else
                                Active until {{ Carbon::createFromTimestamp($stripeSubscription->current_period_end)->toFormattedDateString() }}
                            @endif
                        </div>
                        <p class="fs-6 text-gray-600 fw-semibold mb-6 mb-lg-15">We will send you a notification upon Subscription expiration </p>
                        <!--end::Info-->

                        <!--begin::Notice-->
                        <div class="fs-6 text-gray-600 fw-semibold">
                            {{ $subscription->description ?? '' }}
                        </div>
                        <!--end::Notice-->
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-lg-5">

                        <!--begin::Action-->
                        <livewire:subscription-status/>

                        {{--                        <div class="d-flex justify-content-end align-items-center gap-3 pb-0 px-0"> <!-- Added align-items-center -->--}}
                        {{--                            @if($subscription->onGracePeriod())--}}
                        {{--                                <form method="POST" action="{{ route('subscription.resume') }}" class="m-0">--}}
                        {{--                                    @csrf--}}
                        {{--                                    <button class="btn btn-warning"><i class="fa-solid fa-pause"></i> Pause</button>--}}
                        {{--                                </form>--}}
                        {{--                            @else--}}
                        {{--                                <form method="POST" action="{{ route('subscription.pause') }}" class="m-0">--}}
                        {{--                                    @csrf--}}
                        {{--                                    <button class="btn btn-warning"><i class="fa-solid fa-pause"></i> Pause</button>--}}
                        {{--                                </form>--}}
                        {{--                            @endif--}}

                        {{--                            <form method="POST" action="{{ route('subscription.cancel') }}" class="m-0">--}}
                        {{--                                @csrf--}}
                        {{--                                <button class="btn btn-danger"><i class="fa-solid fa-stop"></i> Cancel</button>--}}
                        {{--                            </form>--}}
                        {{--                        </div>--}}

                        <!--end::Action-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            @endif
        </div>
        <!--end::Card body-->
    </div>

    <!-- Begin: Billing History -->
    <div class="card ">
        <!--begin::Card header-->
        <div class="card-header card-header-stretch border-bottom border-gray-200">
            <!--begin::Title-->
            <div class="card-title">
                <h3 class="fw-bold m-0">Billing History</h3>
            </div>
            <!--end::Title-->

        </div>
        <!--end::Card header-->

        <!--begin::Tab panel-->
        <div id="kt_billing_all" class="card-body p-0 tab-pane" role="tabpanel" aria-labelledby="kt_billing_all">
            <!--begin::Table container-->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table table-row-bordered align-middle gy-4 gs-9">
                    <thead class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                    <tr>
                        <td class="min-w-10px"></td>
                        <td class="min-w-150px">Date</td>
                        <td class="min-w-150px">Amount</td>
                        {{--                        <td class="min-w-150px">Receipt</td>--}}
                        <td></td>
                    </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">

                    @foreach ($invoices as $invoice)
                        <!--begin::Table row-->
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                            <td>{{ $invoice->total() }}</td>
                            {{--                            <td>--}}
                            {{--                                <a href="{{ $invoice->receipt_url }}" target="_blank" class="btn btn-sm btn-light btn-active-light-primary">--}}
                            {{--                                    View Receipt--}}
                            {{--                                </a>--}}
                            {{--                            </td>--}}
                        </tr>
                        <!--end::Table row-->
                    @endforeach

                    </tbody>
                </table>
                <!--end::Table-->
            </div>
            <!--end::Table container-->
        </div>
        <!--end::Tab panel-->
    </div>
    <!-- End: Billing History -->

    <!--end::Tab Content-->
</div>


<!-- Include DataTables Script -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#transactions-table').DataTable({
            paging: true,
            searching: false,
        });
    });
</script>
