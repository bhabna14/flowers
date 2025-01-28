@extends('user.layouts.front-flower-dashboard')


@section('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <style>
        .rejected-status {
            margin-bottom: 20px;
        }

        .rejected-status a {
            color: blue;
            font-weight: bold;
            text-decoration: underline;
        }

        .rejected-text {
            margin-bottom: 20px;
        }

        .order-history-sec .status-text a {
            pointer-events: auto;
        }

        .filter-buttons a {
            margin-right: 10px;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: #c80100;
            border: 1px solid;
        }

        .filter-buttons a.active {
            background-color: #c80100;
            color: #fff;
        }

        .filter-buttons a.active a:hover {
            color: #fff !important;
        }

        .refund-details {
            padding: 10px;
            border: 1px solid #ddd;
            margin: 10px 12px 15px 12px;
            font-weight: 500;
        }

        /* Styling for the 'Active' badge */
        .badge-success {
            background-color: #008009;
            /* Green color for active */
            color: white;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .badge-danger {
            background-color: #c80100;
            /* Green color for active */
            color: white;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .highlighted-text.mt-2 {
            background-color: #ffb837;
            padding: 6px 10px;
            border-radius: 9px;
        }

        /* Styling for the 'Not Started' badge */
        .badge-secondary {
            background-color: #6c757d;
            /* Gray color for not started */
            color: white;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .badge-warning {
            background-color: #ffc107;
            /* Yellow background */
            color: #212529;
            /* Dark text color for contrast */
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            /* Slightly small text size */
            font-weight: 700;
            /* Bold text */

            display: inline-block;
            /* Inline-block for alignment */
            text-align: center;
            /* Center text */
        }

        /* Optional: Add some spacing between the badge and the content */
        .text-right .badge {
            margin-top: 5px;
        }

        /* Modal styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            padding: 20px;
            margin: 10% auto;
            width: 50%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal form {
            display: flex;
            flex-direction: column;
        }

        .modal form div {
            margin-bottom: 15px;
        }

        .modal form label {
            font-weight: bold;
        }

        .modal form input[type="date"] {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal .btn {
            background-color: #c80100;
            color: white;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            margin-top: 10px;
        }

        .modal .btn:hover {
            background-color: #ff5733;
        }

        .text-muted {
            font-size: 12px;
            color: #888;
        }

        .text-strike {
            text-decoration: line-through;
            color: #a5a5a5;
            /* Optional: Grey color for struck-through text */
            margin-right: 10px;
        }

        .text-highlight {
            font-weight: bold;
            color: #2e7d32;
            /* Optional: Green for emphasis */
        }
    </style>
@endsection

@section('content')

    <div class="dashboard__main">
        <div class="dashboard__content bg-light-2">
            <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
                <div class="col-auto">
                    <h1 class="text-30 lh-14 fw-600">Booking History</h1>
                </div>

            </div>
            <div class="row  y-gap-20 justify-between items-end pb-30 lg:pb-40 md:pb-32">
                <div class="col-auto">
                    <div class="filter-buttons">
                        <a href="{{ route('subscription.history') }}"
                            class="{{ request()->routeIs('subscription.history') ? 'active' : '' }}">
                            Subscription History
                        </a>

                        <a href="{{ route('requested.order.history') }}"
                            class="{{ request()->routeIs('requested.order.history') ? 'active' : '' }}">
                            Customized Order History
                        </a>

                    </div>
                </div>
            </div>

            <div class="row">
                @if (session()->has('success'))
                    <div class="alert alert-success" id="Message">
                        {{ session()->get('success') }}
                    </div>
                @endif

                @if ($errors->has('danger'))
                    <div class="alert alert-danger" id="Message">
                        {{ $errors->first('danger') }}
                    </div>
                @endif

                @forelse ($subscriptions as $order)
                    <div class="col-md-12">
                        <div class="order-history-sec">
                            <div class="order-details">
                                <div class="row">
                                    <div class="col-md-3">
                                        SUBSCRIPTION START DATE <br>
                                        {{ \Carbon\Carbon::parse($order->start_date)->format('Y-m-d') }}
                                        <!-- Subscription start date -->
                                    </div>
                                    <div class="col-md-3">
                                        SUBSCRIPTION END DATE <br>
                                        @if ($order->new_date)
                                            <span class="text-strike">
                                                {{ \Carbon\Carbon::parse($order->end_date)->format('Y-m-d') }}
                                            </span>
                                            <span class="text-highlight ms-2">
                                                {{ \Carbon\Carbon::parse($order->new_date)->format('Y-m-d') }}
                                            </span>
                                        @else
                                            {{ \Carbon\Carbon::parse($order->end_date)->format('Y-m-d') }}
                                        @endif
                                    </div>

                                    <div class="col-md-2">
                                        TOTAL PAYMENT <br>
                                        ₹ {{ number_format($order->order->total_price), 2 }}
                                        <!-- Total payment from flowerPayments -->
                                    </div>
                                    <div class="col-md-2 text-right">
                                        ORDER NUMBER <br>
                                        <span style="font-size: 15px">#{{ $order->order_id }}</span> <!-- Order number -->
                                    </div>
                                    <div class="col-md-2 text-center" style="   ">


                                        @if ($order->status === 'pending')
                                            <span class="badge badge-warning">
                                                Your subscription has not started yet
                                            </span>
                                        @else
                                            <span
                                                class="badge 
                                {{ $order->status === 'active' ? 'badge-success' : ($order->status === 'paused' ? 'badge-danger' : 'badge-danger') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif



                                    </div>

                                </div>
                            </div>
                            <div class="row order-details-booking">
                                <div class="col-md-2">
                                    <img src="{{ $order->flowerProducts->product_image_url }}" alt="Product Image" />
                                    <!-- Display product image -->
                                </div>
                                <div class="col-md-7">
                                    <h6>{{ $order->flowerProducts->name }}</h6> <!-- Subscription name -->
                                    <p>{{ $order->flowerProducts->description }}</p> <!-- Subscription description -->
                                </div>
                                <div class="col-md-3">
                                    <a href="{{ route('subscription.details', ['subscription_id' => $order->id, 'order_id' => $order->order_id]) }}"
                                        class="button px-10 fw-400 text-14 pay-button-bg h-50 text-white">
                                        View Details
                                    </a>
                                    @if ($order->status == 'active')
                                        @if ($order->pause_start_date && $order->pause_end_date)
                                            <a href="{{ route('subscription.pauseedit', $order->id) }}"
                                                class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white pause-button"
                                                style="margin-bottom: 10px; background-color: #c80100 !important;">
                                                Edit Pause
                                            </a>
                                        @else
                                            <a href="{{ route('subscription.pausepage', $order->id) }}"
                                                class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white pause-button"
                                                style="margin-bottom: 10px; background-color: #c80100 !important;">
                                                Pause
                                            </a>
                                        @endif
                                    @elseif ($order->status == 'paused')
                                        <a href="{{ route('subscription.pauseedit', $order->id) }}"
                                            class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white pause-button"
                                            style="margin-bottom: 10px; background-color: #c80100 !important;">
                                            Edit Pause
                                        </a>
                                        <a href="{{ route('subscription.resumepage', $order->id) }}"
                                            class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white resume-button"
                                            style="margin-bottom: 10px; background-color: #c80100 !important;">
                                            Resume
                                        </a>
                                    @endif
                                </div>

                            </div>
                            @if ($order->status === 'paused')
                                <div class="highlighted-text mt-2">
                                    <strong>Note:</strong> Your subscription is paused from
                                    <span
                                        class="text-highlight">{{ \Carbon\Carbon::parse($order->pause_start_date)->format('Y-m-d') }}</span>
                                    to
                                    <span
                                        class="text-highlight">{{ \Carbon\Carbon::parse($order->pause_end_date)->format('Y-m-d') }}</span>.
                                </div>
                            @endif
                        </div>

                    </div>

                @empty
                    <p>No subscription orders found.</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection

@section('scripts')
@endsection
