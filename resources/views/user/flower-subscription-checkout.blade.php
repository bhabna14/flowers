@extends('user.layouts.front-flower')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- jQuery UI CSS for datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

<!-- jQuery Timepicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .ui-datepicker {
        font-size: 16px;
    }
    .form-control {
        cursor: pointer;
    }
    .ui-datepicker td a {
        cursor: pointer;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .mt-10 {
        margin-top: 10px;
    }
    .pt-30 {
        padding-top: 30px;
    }
</style>
@endsection

@section('content')
<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper">
                <h1 class="sc-7kepeu-0 kYnyFA description">BOOK NOW</h1>
            </div>
        </div>
</section>

<section class="booking-form mt-30 mb-30">
    <div class="container">
        <div class="row">
            <h4 class="mb-20">Order Now</h4>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Okay'
                });
            </script>
        @endif

        @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Subscription Activated Successfully',
                text: '{{ session('success') }}',
                confirmButtonText: 'Okay'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the booking history page
                    window.location.href = '{{ route('subscription.history') }}'; // Make sure to use the correct route name
                }
            });
        </script>
         @endif
            <div class="col-md-7">
                <form action="{{ route('booking.flower.subscription') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="duration" value="{{ $product->duration }}">
                    <input type="hidden" name="price" value="{{ $product->price }}">
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                  
                
                    <div class="row">
                        <div class="form-input mt-20 col-md-12"  style="margin-bottom: 0px !important">
                            <label for="date">Please Select the Date <span style="color:red">*</span></label>
                            <input type="text" name="start_date"  placeholder="Please Select The Date" class="form-control" id="date" required>
                        </div>
                        <div class="error_date"></div>
                       
                        <div class="form-input mt-30 col-md-12">
                            <label for="suggestion">Suggestions</label>
                            <textarea name="suggestion" id="suggestion" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="your-address-list">
                                
                                    @foreach ($addresses as $address)
                                        <div class="your-address">
                                            <input type="radio" name="address_id" id="address{{ $address->id }}" value="{{ $address->id }}" required>
                                            <label for="address{{ $address->id }}">
                                                <div class="address-type">{{ $address->address_type }}</div>
                                                {{ $address->apartment_flat_plot ?? "" }}, {{ $address->localityDetails->locality_name ?? 'N/A' }},
                                                {{ $address->landmark ?? "" }}<br>
                                                {{ $address->city }}, {{ $address->state }}, {{ $address->country }}, {{ $address->pincode }}
                                                @if($address->default == 1)
                                                    <div class="default-badge">Default</div>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                               
                            </div>
                        </div>
                    </div>
                    <div id="errorMessage" style="color: red; display: none;">
                        Please add an address to continue with your order.
                    </div>
                    
                    
                    <div class="row" style="margin-top:20px; margin-bottom: 24px;">
                        <div class="col-md-4">
                            <a href="#" class="add-address-btn" id="addAddressBtn"><i class="fa fa-plus"></i> Add Address</a>
                        </div>
                    </div>
                
                    <button type="button" id="payButton" class="button -md -blue-1 bg-dark-3 text-white mt-20">Pay with Razorpay</button>

                </form>
                
           
            </div>
            <div class="col-xl-5 col-lg-5">
                <div class="md:ml-0">
                    <div class="px-30 py-30 border-light rounded-4">
                        <div class="text-20 fw-500 mb-30">Your Subscription Details</div>
                        <div class="row x-gap-15 y-gap-20">
                            <div class="col-auto">
                                <!-- Display the product or pandit's photo -->
                                <img src="{{ asset('storage/'.$product->product_image ?? 'default-image.jpg') }}" alt="Subscription Image" class="size-140 rounded-4 object-cover">
                            </div>
                            <div class="col">
                                <div class="lh-17 fw-500">{{ $product->name }}</div>
                                {{-- <input type="hidden" class="form-control" name="product_id" value="{{ $product_id }}"> --}}
                                
                                <div class="text-16 lh-15 mt-5 fw-600">
                                    Total Fee: ₹{{ sprintf('%.2f', $product->price) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="addressModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <div class="row">
            <div class="col-md-12">
                <form id="addressForm" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Type</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="individual" name="place_category" value="Indivisual" required>
                                <label class="form-check-label" for="individual">
                                    <span class="custom-radio"></span>
                                    Individual
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="apartment" name="place_category" value="Apartment" required>
                                <label class="form-check-label" for="apartment">
                                    <span class="custom-radio"></span>
                                    Apartment
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="business" name="place_category" value="Business" required>
                                <label class="form-check-label" for="business">
                                    <span class="custom-radio"></span>
                                    Business
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-check custom-radio-button">
                                <input type="radio" class="form-check-input" id="temple" name="place_category" value="Temple" required>
                                <label class="form-check-label" for="temple">
                                    <span class="custom-radio"></span>
                                    Temple
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="apartment_flat_plot" class="form-label">Apartment/Flat/Plot</label>
                            <input type="text" class="form-control" id="apartment_flat_plot" name="apartment_flat_plot" placeholder="Enter details" required>
                        </div>
                        <div class="col-md-6">
                            <label for="landmark" class="form-label">Landmark</label>
                            <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Enter landmark" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="locality" class="form-label">Locality</label>
                            <select class="form-control" id="locality" name="locality" required>
                                <option value="">Select Locality</option>
                                @foreach($localities as $locality)
                                    <option value="{{ $locality->unique_code }}" data-pincode="{{ $locality->pincode }}">
                                        {{ $locality->locality_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter pincode" required pattern="\d{6}" readonly>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Town/City</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" name="city" placeholder="Enter Town/City *" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">State</label>
                                <select name="state" class="form-control" required>
                                    <option value="Odisha">Odisha</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Address Type</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="rdiobox">
                                <input name="address_type" type="radio" value="Home" required> <span>Home</span>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label class="rdiobox">
                                <input name="address_type" type="radio" value="Work" required> <span>Work</span>
                            </label>
                        </div>
                        <div class="col-lg-2">
                            <label class="rdiobox">
                                <input name="address_type" type="radio" value="Other" required checked> <span>Other</span>
                            </label>
                        </div>
                    </div>
                    <div class="d-inline-block">
                        <button type="submit" id="saveAddressBtn" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                            Save Address
                        </button>
                        
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')


<!-- jQuery library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- jQuery UI library for datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- jQuery Timepicker plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>


<script>
$(document).ready(function() {
// Function to get tomorrow's date in yyyy-mm-dd format
function getTomorrowDate() {
    var today = new Date();
    today.setDate(today.getDate() + 1); // Move to tomorrow
    var dd = today.getDate();
    var mm = today.getMonth() + 1; // January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }

    return yyyy + '-' + mm + '-' + dd;
}

// Initialize datepicker and timepicker
$("#date").datepicker({
    dateFormat: "yy-mm-dd",
    minDate: getTomorrowDate(),  // Set minDate to tomorrow
    onSelect: function(dateText) {
        console.log("Date selected: " + dateText);
        // Update the timepicker when a date is selected
        $("#time").timepicker('option', 'minTime', getMinTime());
    }
});


    // Function to calculate current time + 2 hours and format it for the timepicker
    function getMinTime() {
        const now = new Date();
        now.setHours(now.getHours() + 2);
        now.setMinutes(0); // Round minutes to the nearest 15 if needed

        let hours = now.getHours();
        let minutes = now.getMinutes();
        if (minutes < 10) minutes = '0' + minutes;
        if (hours < 10) hours = '0' + hours;

        return `${hours}:${minutes}`;
    }

    // Initialize timepicker
    $("#time").timepicker({
        timeFormat: 'h:i A', // 12-hour format with AM/PM
        step: 15, // Interval for selectable times
        minTime: getMinTime(), // Start time is 2 hours from current time
        maxTime: '23:59',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });

    // Open address modal
    $("#addAddressBtn").click(function() {
        $("#addressModal").show();
    });

    // Close the modal
    $("#closeModal").click(function() {
        $("#addressModal").hide();
    });

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target == document.getElementById("addressModal")) {
            $("#addressModal").hide();
        }
    };

   // Handle address form submission
$('#addressForm').on('submit', function (e) {
    e.preventDefault(); // Prevent the form from submitting normally
    var formData = new FormData(this); // Collect form data

    // AJAX request to submit the form
    $.ajax({
        url: '{{ route('savefrontaddress') }}', // The route to handle form submission
        type: 'POST',
        data: formData,
        processData: false, // Prevent jQuery from transforming the data
        contentType: false, // Don't set content type for FormData
        success: function (response) {
            if (response.success) {
                // Close the modal if the address was saved successfully
                $('#addressModal').hide();

                // Show success message with SweetAlert
                Swal.fire({
                    title: 'Success!',
                    text: 'Address saved successfully!',
                    icon: 'success',
                    confirmButtonText: 'Okay'
                }).then((result) => {
                    // Reload the page after the alert is dismissed
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });

                // Dynamically append the new address to the address list
                var localityName = response.address.locality ? response.address.locality : 'N/A';
                var newAddressHtml = `
                    <div class="your-address">
                        <input type="radio" name="address_id" id="address${response.address.id}" value="${response.address.id}" required>
                        <label for="address${response.address.id}">
                            <div class="address-type">${response.address.address_type}</div>
                            ${response.address.apartment_flat_plot ?? ""}, ${localityName},
                            ${response.address.landmark ?? ""}<br>
                            ${response.address.city}, ${response.address.state}, ${response.address.country}, ${response.address.pincode}
                            ${response.address.default == 1 ? '<div class="default-badge">Default</div>' : ''}
                        </label>
                    </div>`;

                // Append the new address to the 'your-address-list' container
                $('.your-address-list').append(newAddressHtml);

                // Hide the empty warning message if there are now addresses
                if ($('.your-address-list .your-address').length > 0) {
                    $('.alert-warning').hide();
                }

                // Reset the form fields
                $('#addressForm')[0].reset();
            } else {
                // If there was an error saving the address, show an error message with SweetAlert
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error saving the address.',
                    icon: 'error',
                    confirmButtonText: 'Okay'
                });
            }
        },
        error: function (xhr, status, error) {
            // Show an error message if the AJAX request fails
            Swal.fire({
                title: 'Error!',
                text: 'There was an error processing your request. Please try again later.',
                icon: 'error',
                confirmButtonText: 'Okay'
            });
        }
    });
});

});

</script>
<script>
    document.getElementById('locality').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var pincode = selectedOption.getAttribute('data-pincode');
  
        if (pincode) {
            document.getElementById('pincode').value = pincode;
        } else {
            document.getElementById('pincode').value = '';
        }
    });
  </script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('payButton').onclick = function (e) {
        e.preventDefault();
    
        console.log('Pay button clicked. Validating fields...');
    
        // Get the form and input fields
        var form = document.getElementById('bookingForm');
        var startDate = form.querySelector('[name="start_date"]');
        var addressId = form.querySelector('[name="address_id"]:checked');
        var suggestion = form.querySelector('[name="suggestion"]');
        var errorDate = form.querySelector('.error_date');
    
        // Validation logic
      
        if (!startDate.value) {
            displayError(errorDate, 'Please select a date.');
            isValid = false;
            return;
        }
    
        // Validation for address selection
        if (!addressId) {
            var addressSection = form.querySelector('.your-address-list');
            displayError(addressSection, 'Please select an address.');
            isValid = false;
            return;
        }
        // if (suggestion && suggestion.value.trim() === "") {
        //     alert('Please provide your suggestion or leave it blank.');
        // }
    
        console.log('Fields validated successfully. Initializing Razorpay...');
    
        var amount = {{ ($product->price) * 100 }}; // Amount in paise
    
        var options = {
            "key": "{{ config('services.razorpay.key') }}",
            "amount": amount,
            "name": "33 Crores",
            "description": "",
            "image": "{{ asset('front-assets/img/brand/logo.png') }}",
            "handler": function (response) {
                console.log('Payment handler triggered.');
                console.log('Payment ID:', response.razorpay_payment_id);
    
                // Add the Razorpay payment ID to the form and submit it
                if (form) {
                    console.log('Form found. Appending payment ID...');
                    form.appendChild(createHiddenInput('razorpay_payment_id', response.razorpay_payment_id));
                    console.log('Submitting form...');
                    form.submit();
                } else {
                    console.error('Form not found!');
                }
            },
            "prefill": {
                "name": "{{ Auth::guard('users')->user()->name }}",
                "contact": "{{ Auth::guard('users')->user()->mobile_number }}"
            },
            "theme": {
                "color": "#F37254"
            }
        };
    
        var rzp1 = new Razorpay(options);
        console.log('Opening Razorpay checkout...');
        rzp1.open();
    };
    
    function createHiddenInput(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

  
    
    function displayError(element, message) {
        // Create the error message element
        var error = document.createElement('div');
        error.className = 'error-message';
        error.style.color = 'red';
        error.style.fontWeight = 'bold';
        error.style.marginTop = '5px';
        error.textContent = message;
    
        // Insert the error message after the element
        if (element.nextSibling) {
            element.parentNode.insertBefore(error, element.nextSibling);
        } else {
            element.parentNode.appendChild(error);
        }
    }
    
    function clearErrors() {
        var errors = document.querySelectorAll('.error-message');
        errors.forEach(function (error) {
            error.remove();
        });
    }
</script>

@endsection
