@extends('user.layouts.front-flower-dashboard')


@section('styles')
    <style>
        .tabs__pane {
            margin-bottom: 27px;
        }
    </style>
@endsection

@section('content')

    <div class="dashboard__main">
        <div class="dashboard__content bg-light-2">
            <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-32">
                <div class="col-auto">

                    <h1 class="text-30 lh-14 fw-600">Add Address</h1>

                </div>

                <div class="col-auto">

                </div>
            </div>


            <div class="py-20 px-30 rounded-4 bg-white shadow-3">
                <div class="tabs -underline-2 js-tabs">


                    <div class="tabs__content js-tabs-content">
                        <div class="tabs__pane -tab-item-1 is-tab-el-active">
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

                            <form action="{{ route('saveuseraddress') }}" method="post" enctype="multipart/form-data">
                                @csrf

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

                    <div class="d-inline-block">

                        <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                            Save Address<div class="icon-arrow-top-right ml-15"></div>
                        </button>

                    </div>

                    </form>
                </div>
            </div>
        </div>



    </div>
    </div>

   
@section('scripts')
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
            option.value = apartment.id;
            option.textContent = apartment.apartment_name;
            apartmentDropdown.appendChild(option);
        });
    });
</script>

<script>
    // Hide success message after 3 seconds
    setTimeout(function () {
        document.getElementById('Message').style.display = 'none';
    }, 3000);
</script>
@endsection
