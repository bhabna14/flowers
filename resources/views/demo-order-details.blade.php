@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">ADD Product</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-product') }}"
                        class="btn btn-warning text-dark">Manage Product</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">ADD Product</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

    <form action="{{ route('') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- user details --}}
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">User Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter User name" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Phone Number</label>
                <input type="number" name="name" class="form-control" id="name" placeholder="Phone Number" required>
            </div>

            {{-- user address --}}

            <div class="row ">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Type</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check custom-radio-button">
                        <input type="radio" class="form-check-input" id="individual"
                            name="place_category" value="Indivisual" required>
                        <label class="form-check-label" for="individual">
                            <span class="custom-radio"></span>
                            Individual
                        </label>
                    </div>


                </div>
                <div class="col-lg-2">
                    <div class="form-check custom-radio-button">
                        <input type="radio" class="form-check-input" id="apartment"
                            name="place_category" value="Apartment">
                        <label class="form-check-label" for="apartment">
                            <span class="custom-radio"></span>
                            Apartment
                        </label>
                    </div>
                </div>
                <div class="col-lg-2">

                    <div class="form-check custom-radio-button">
                        <input type="radio" class="form-check-input" id="business"
                            name="place_category" value="Business">
                        <label class="form-check-label" for="business">
                            <span class="custom-radio"></span>
                            Business
                        </label>
                    </div>
                </div>
                <div class="col-lg-2">

                    <div class="form-check custom-radio-button">
                        <input type="radio" class="form-check-input" id="temple"
                            name="place_category" value="Temple">
                        <label class="form-check-label" for="temple">
                            <span class="custom-radio"></span>
                            Temple
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 ">
                    <label for="apartment_flat_plot" class="form-label">Apartment - No/Flat - No/Plot -
                        No</label>
                    <input type="text" class="form-control" id="apartment_flat_plot"
                        name="apartment_flat_plot" placeholder="Enter details" required>
                </div>
                <div class="col-md-6 ">
                    <label for="landmark" class="form-label">Landmark</label>
                    <input type="text" class="form-control" id="landmark" name="landmark"
                        placeholder="Enter landmark" required>
                </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-4">
                  <label for="locality" class="form-label">Locality</label>
                  <select class="form-control" id="locality" name="locality" required>
                      <option value="">Select Locality</option>
                      @foreach ($localities as $locality)
                          <option value="{{ $locality->id }}" data-pincode="{{ $locality->pincode }}">
                              {{ $locality->locality_name }}
                          </option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-4">
                  <label for="apartment_name">Apartment Name</label>
                  <select class="form-control" id="apartment_name" name="apartment_name" required>
                      <option value="">Select Apartment</option>
                  </select>
              </div>
              <div class="col-md-4">
                  <label for="pincode" class="form-label">Pincode</label>
                  <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" readonly>
              </div>
          </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Town/City </label>
                        <input type="text" class="form-control" id="exampleInputEmail1"
                            value="" name="city" placeholder="Enter Town/City *" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">State</label>
                        <select name="state" class="form-control" id="">
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
                    <label class="rdiobox"><input name="address_type" type="radio" value="Home">
                        <span>Home</span></label>
                </div>
                <div class="col-lg-2">
                    <label class="rdiobox"><input name="address_type" type="radio" value="Work">
                        <span>Work</span></label>
                </div>
                <div class="col-lg-2">
                    <label class="rdiobox"><input checked name="address_type" type="radio"
                            value="Other"> <span>Other</span></label>
                </div>
            </div>


    </div>
</div>
{{-- order details --}}

<div class="col-md-6 mb-3">
    <label for="duration" class="form-label">Flower List</label>
    <select name="duration" id="duration" class="form-control select2" >
    
    </select>
</div>
            <div class="col-md-6 mb-3">
                <label for="duration" class="form-label">Quantity</label>
                <select name="duration" id="duration" class="form-control select2" >
                    <option value="" disabled selected>Select Quantity</option>
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                   
                </select>
            </div>

          
        </div>
    </form>
@endsection

@section('modal')
@endsection

@section('scripts')
    <!-- Form-layouts js -->
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Initialize Select2 for dropdowns
        });
    </script>

    <script>
        const apartments = @json($apartments);
    
        // Event listener for locality dropdown
        document.getElementById('locality').addEventListener('change', function () {
            const localityId = this.value;
            const selectedOption = this.options[this.selectedIndex];
    
            // Update the pincode field
            const pincode = selectedOption.getAttribute('data-pincode');
            document.getElementById('pincode').value = pincode || '';
    
            // Filter apartments based on the selected locality
            const filteredApartments = apartments.filter(apartment => apartment.locality_id == localityId);
    
            // Populate the apartment dropdown
            const apartmentDropdown = document.getElementById('apartment_name');
            apartmentDropdown.innerHTML = '<option value="">Select Apartment</option>';
            filteredApartments.forEach(apartment => {
                const option = document.createElement('option');
                option.value = apartment.apartment_name;
                option.textContent = apartment.apartment_name;
                apartmentDropdown.appendChild(option);
            });
        });
    </script>
@endsection
