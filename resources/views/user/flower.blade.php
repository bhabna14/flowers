@extends('user.layouts.front-flower')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
        }

        .product-title {
            font-size: 1.5rem;
            color: #333;
        }

        .product-price {
            font-size: 1.25rem;
            color: #0275d8;
        }

        .remaining-time {
            font-size: 1.1rem;
        }

        .btn-primary {
            background: linear-gradient(90deg, #ff7e5f 0%, #feb47b 100%);
            border: none;
        }

        .product-card {
            border-radius: 15px;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            padding-bottom: 25px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
            /* height: 770px; */
        }

        .product-car_cust {
            border-radius: 15px;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
            /* height: 600px; */

        }

        .text-decoration-line-through {
            text-decoration: line-through;
            /* Strikethrough for the MRP */
            color: #aaa;
            /* Light grey for the crossed-out MRP */
        }

        .product-image-container {
            position: relative;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            /* height: 250px; */
            object-fit: cover;
            transition: transform 0.3s;
        }

        .product-image-container:hover .product-image {
            transform: scale(1.1);
        }

        .badge-sale {
            top: 10px;
            left: 10px;
            background-color: #ff5c5c;
            color: #fff;
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .product-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .product-description {
            color: #777;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 20px;
            color: #B90B0B;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #B90B0B, #feb47b);
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 30px;
            transition: background 0.3s;
            letter-spacing: 1px
        }

        .flower-package-heading h1 {
            color: #000;
            text-align: center;
            letter-spacing: 1px;
        }
    </style>
@endsection

@section('content')


    <section class="" style="margin-top: -17px;">
        <div class="">
            <!-- Home Banner Section -->
            <div class="home-banner-section">
                <div id="homeBannerCarousel" class="owl-carousel owl-theme">
                    @foreach ($banners as $banner)
                        <div class="item">
                            <img src="{{ $banner['banner_img_url'] }}" alt="{{ $banner['alt_text'] ?? 'Home Banner' }}"
                                class="img-fluid d-block w-100">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-12">
                    <div class="flower-package-heading">
                        <h1>Customized Flower</h1>
                        <img src="{{ asset('front-assets/img/general/hr.png') }}" alt="" class="border-bottom-img">
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 20px; margin-bottom: 140px;">
                @if ($customizedpps->isNotEmpty())
                    @foreach ($customizedpps as $customizedpp)
                        <div class="product-car_cust row shadow-lg position-relative">
                            <div class="col-md-3 mb-4">

                                <div class="product-image-container">
                                    <img src="{{ $customizedpp->product_image }}" alt="{{ $customizedpp->name }}"
                                        class="product-image">

                                </div>


                            </div>
                            <div class="col-md-9">
                                <div class="card-body ">
                                    <h5 class="product-title">{{ $customizedpp->name }}</h5>
                                    <p class="product-description">{{ $customizedpp->description }}</p>
                                    <p class="product-price">
                                        {{ $customizedpp->immediate_price }}
                                    </p>


                                    @if (Auth::guard('users')->check())
                                        <a href="{{ route('cutsomized-checkout', ['product_id' => $customizedpp->product_id]) }}"
                                            class="btn btn-gradient w-100 mt-2">
                                            Order Now
                                        </a>
                                    @else
                                        <a href="{{ route('userlogin', ['referer' => urlencode(route('cutsomized-checkout', ['product_id' => $customizedpp->product_id]))]) }}"
                                            class="btn btn-gradient w-100 mt-2">
                                            Order Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-center">No products available at the moment.</p>
                @endif
            </div>
        </div>
    </section>


    <section>
        <div class="container">
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-12 text-center">
                    <h1 class="display-4 font-weight-bold">Subscription Details</h1>
                    <img src="{{ asset('front-assets/img/general/hr.png') }}" alt=""
                        class="border-bottom-img my-3">
                </div>
            </div>

            <div class="row" style="margin-top: 20px; margin-bottom: 140px;">
                @auth('users')
                    @if ($currentOrders->isNotEmpty())
                        @foreach ($currentOrders as $subscription)
                            <div class="product-car_cust row shadow-lg position-relative p-4 rounded-lg mb-5 bg-white">
                                <div class="col-md-3">
                                    <div class="product-image-container text-center">
                                        <img src="{{ $subscription->flowerProduct->product_image_url ?? asset('default-image.jpg') }}"
                                            alt="{{ $subscription->flowerProduct->name ?? 'Product Image' }}"
                                            class="img-fluid rounded shadow">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">

                                        <div class="remaining-time bg-gradient-primary text-white p-3 rounded text-center mb-3">
                                            <strong>Remaining Time:</strong>
                                            <span id="timer-{{ $loop->index }}"
                                                data-endtime="{{ \Carbon\Carbon::parse($subscription->subscription->display_end_date)->format('Y-m-d H:i:s') }}">
                                                Loading...
                                            </span>
                                        </div>

                                        <h5 class="product-title font-weight-bold">
                                            {{ $subscription->flowerProduct->name ?? 'No Product Name' }}</h5>
                                        <p class="product-price text-primary font-weight-bold">
                                            Price: ₹{{ $subscription->flowerProduct->price ?? 'N/A' }}
                                        </p>
                                        <p class="subscription-dates text-secondary">
                                            <strong>From: </strong>
                                            {{ \Carbon\Carbon::parse($subscription->subscription->start_date)->format('d M Y') ?? 'N/A' }}
                                            <strong>To: </strong>
                                            {{ \Carbon\Carbon::parse($subscription->subscription->display_end_date)->format('d M Y') ?? 'N/A' }}
                                        </p>
                                        
                                       @if($subscription->subscription->status === 'expired')
                                            <div style="margin-top: 30px">
                                                <a class="mt-4" href="{{ route('renew.checkout', ['product_id' => $subscription->product_id, 'order_id' => $subscription->order_id]) }}" 
                                                    style="background: linear-gradient(90deg, #2196f3, #64b5f6); 
                                                                        color: white; 
                                                                        border: none; 
                                                                        border-radius: 8px; 
                                                                        font-size: 16px; 
                                                                        padding: 12px; 
                                                                        cursor: pointer; 
                                                                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                                                                        transition: transform 0.2s, box-shadow 0.2s;">
                                                                Renew Subscription
                                                            </a>
                                                    @else
                                                    <button type="submit"
                                                                style="background: linear-gradient(90deg, #efb756, #fd3f3c); 
                                                                        color: white; 
                                                                        border: none; 
                                                                        border-radius: 8px; 
                                                                        font-size: 16px; 
                                                                        padding: 12px; 
                                                                        cursor: pointer; 
                                                                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
                                                                        transition: transform 0.2s, box-shadow 0.2s;">{{$subscription->subscription->status}}
                                                            </button>
                                            </div>

                                       
                                        @endif                                           
                                    </div>
                                </div>
                            </div>

                            
                        @endforeach
                    @else
                        <p class="text-center">No subscriptions available at the moment.</p>
                    @endif
                @else
                    <p class="text-center">Please <a href="{{ route('userlogin') }}">log in</a> to view your subscriptions.</p>
                @endauth
            </div>
        </div>
    </section>


    <section style="margin-top: -60px;">
        <div class="container">
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-12">
                    <div class="flower-package-heading">
                        <h1>Flower Package</h1>
                        <img src="{{ asset('front-assets/img/general/hr.png') }}" alt="" class="border-bottom-img">

                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 20px; margin-bottom: 140px;">
                @if ($products->isNotEmpty())
                    @foreach ($products as $product)
                        <div class="col-md-4 mb-4">
                            <a href="{{ route('product.productdetails', ['slug' => $product->slug]) }}"
                                class="product-link">

                                <div class="product-card shadow-lg position-relative">
                                    <div class="product-image-container">
                                        <img src="{{ $product->product_image }}" alt="{{ $product->name }}"
                                            class="product-image">

                                    </div>
                                    <div class="card-body text-center">
                                        <h5 class="product-title">{{ $product->name }}</h5>
                                        <p class="product-description">{{ $product->description }}</p>
                                        <p class="product-price">
                                            <span class="text-decoration-line-through">₹
                                                {{ number_format($product['mrp'], 2) }}</span>
                                            ₹ {{ number_format($product['price'], 2) }}
                                        </p>

                                        @if (Auth::guard('users')->check())
                                            <a href="{{ route('checkout', ['product_id' => $product->product_id]) }}"
                                                class="btn btn-gradient w-100 mt-2">
                                                Order Now
                                            </a>
                                        @else
                                            <a href="{{ route('userlogin', ['referer' => urlencode(route('checkout', ['product_id' => $product->product_id]))]) }}"
                                                class="btn btn-gradient w-100 mt-2">
                                                Order Now
                                            </a>
                                        @endif


                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p class="text-center">No products available at the moment.</p>
                @endif
            </div>
        </div>
    </section>

    <section class="upcoming-bg">
        <div class="container">
            <div class="row">
                <!-- Embed YouTube Video with Full Coverage and Autoplay -->
                <div class="col-12">
                    {{-- <iframe width="560" height="315" src="https://www.youtube.com/embed/UUMc1pkxLuI?si=hrBMJvMc_gu-Kax9" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> --}}
                    <iframe width="100%" height="100%"
                        src="https://www.youtube.com/embed/UUMc1pkxLuI?autoplay=1&rel=0&showinfo=0&modestbranding=1"
                        frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonial-bg">
        <div data-anim-wrap class="container">
            <div class="row justify-center text-center">
                <div class="col-auto">
                    <div class="sectionTitle text-white">
                        <h2 class="sectionTitle__title">Blessed Reviews</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">Hear from our happy devotees who made their rituals
                            extraordinary with 33crores!</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden pt-60 lg:pt-40 sm:pt-30 js-section-slider" data-gap="30"
                data-slider-cols="xl-3 lg-3 md-2 sm-1 base-1">
                <div class="swiper-wrapper">

                    <div class="swiper-slide">
                        <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">

                            <p class="testimonials__text lh-18 fw-500 text-dark-1">33crores has transformed my pooja
                                preparations! The customized flower subscription is a blessing – fresh, vibrant, and
                                delivered on time. It saves me the hassle of last-minute shopping and keeps my rituals
                                stress-free. Truly a divine experience!</p>

                            <div class="pt-20 mt-28 border-top-light">
                                <div class="row x-gap-20 y-gap-20 items-center">
                                    {{-- <div class="col-auto">
                        <img class="size-60" src="img/avatars/1.png" alt="image">
                      </div> --}}

                                    <div class="col-auto">
                                        <div class="text-15 fw-500 lh-14">— Anuradha Mohanty</div>
                                        {{-- <div class="text-14 lh-14 text-light-1 mt-5">Web Designer</div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">

                            <p class="testimonials__text lh-18 fw-500 text-dark-1">33crores has transformed my daily puja
                                rituals! The flowers are always fresh and beautifully arranged, perfectly suited to each
                                deity I worship. The convenience of having them delivered right to my doorstep has saved me
                                so much time and effort. Their customer service is exceptional, always ready to help with
                                any special requests. I feel more connected to my traditions thanks to 33crores!</p>

                            <div class="pt-20 mt-28 border-top-light">
                                <div class="row x-gap-20 y-gap-20 items-center">
                                    {{-- <div class="col-auto">
                        <img class="size-60" src="img/avatars/1.png" alt="image">
                      </div> --}}

                                    <div class="col-auto">
                                        <div class="text-15 fw-500 lh-14">— Vishal Behera</div>
                                        {{-- <div class="text-14 lh-14 text-light-1 mt-5">Web Designer</div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">

                            <p class="testimonials__text lh-18 fw-500 text-dark-1">As someone who leads a busy life,
                                33crores’ flower subscription service has been a blessing. The timely deliveries ensure that
                                my puja setups are always perfect, without any last-minute hassles. The attention to detail
                                and the freshness of the blooms truly reflect the company’s dedication to their customers. I
                                highly recommend 33crores to anyone looking to enhance their spiritual practices with ease
                                and elegance!</p>

                            <div class="pt-20 mt-28 border-top-light">
                                <div class="row x-gap-20 y-gap-20 items-center">
                                  
                                    <div class="col-auto">
                                        <div class="text-15 fw-500 lh-14">— Madhuchanda Das</div>
                                    </div>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- cta- --}}
    <div class="section-cta-custom pt-0">
        <div class="container">
            <div class="section-title text-center">
                <p class="subtitle">How We Can Assist</p>
                <h4 class="title">We Are Ready To Assist </h4>
            </div>
            <div class="row align-items-center position-relative">
                <div class="col-md-6">
                    <div class="sigma_cta primary-bg">
                        {{-- <img class="cta-left-img" src="{{ asset('front-assets/img/4ebcc66a-bcc1-429f-8ef1-6040ae9f369d-removebg-preview.png')}}" alt="cta"> --}}
                        <div class="sigma_cta-content" style="    padding: 40px 40px 40px 148px;">

                            <h4 class="text-white">+91 9776888887</h4>
                        </div>
                    </div>
                </div>
                <span class="sigma_cta-sperator d-lg-flex">or</span>
                <div class="col-md-6">
                    <div class="sigma_cta primary-bg1">
                        <div class="sigma_cta-content" style="padding: 40px 40px 40px 90px;">
                            <h4 class="text-white">contact@33crores.com</h4>
                        </div>
                        {{-- <img class="cta-left-img" src="{{ asset('front-assets/img/4ebcc66a-bcc1-429f-8ef1-6040ae9f369d-removebg-preview.png')}}" alt="cta"> --}}

                    </div>
                </div>
            </div>

        </div>
    </div>



    <section class="pt-60 custmer-count">
        <div class="container">
            <div class=" pb-40">
                <div class="row justify-center text-center" style="margin-top: 30px">

                    <div class="col-xl-3 col-6">
                        <img src="{{ asset('images/1.png') }}" alt="image" width="50%">
                        {{-- <div class="text-40 lg:text-30 lh-13 fw-600 counter">101</div> --}}
                        <div class="text-14 lh-14 text-light-1 mt-5 "
                            style="text-transform: capitalize;font-size: 18px !important;
    letter-spacing: 1px; ">
                            Freshness Guaranteed</div>
                    </div>

                    <div class="col-xl-3 col-6">
                        <img src="{{ asset('images/2.png') }}" alt="image" width="50%">
                        {{-- <div class="text-40 lg:text-30 lh-13 fw-600 counter">791</div> --}}
                        <div class="text-14 lh-14 text-light-1 mt-5"
                            style="text-transform: capitalize; font-size: 18px !important;
    letter-spacing: 1px;">
                            Customizable Plans</div>
                    </div>

                    <div class="col-xl-3 col-6">
                        <img src="{{ asset('images/3.png') }}" alt="image" width="50%">
                        {{-- <div class="text-40 lg:text-30 lh-13 fw-600 counter">121</div> --}}
                        <div class="text-14 lh-14 text-light-1 mt-5"
                            style="text-transform: capitalize;font-size: 18px !important;
    letter-spacing: 1px; ">Timely
                            Delivery</div>
                    </div>

                    <div class="col-xl-3 col-6">
                        <img src="{{ asset('images/4.png') }}" alt="image" width="50%">
                        {{-- <div class="text-40 lg:text-30 lh-13 fw-600 counter">1491</div> --}}
                        <div class="text-14 lh-14 text-light-1 mt-5"
                            style="text-transform: capitalize;font-size: 18px !important;
    letter-spacing: 1px; ">
                            Eco-Friendly Packaging</div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <section class="section-bg pt-80 pb-80 md:pt-40 md:pb-40">


        <div class="container">
            <div class="row y-gap-30 items-center justify-between">
                <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-delay="500">
                    <h2 class="text-30 lh-15">Download the App</h2>
                    <p class="text-dark-1 pr-40 lg:pr-0 mt-15 sm:mt-5">Simplify your spiritual rituals with our Fresh Pooja
                        Flower Subscription App! Enjoy hassle-free access to fresh, handpicked flowers delivered straight to
                        your doorstep. Personalize your subscription, track deliveries, and stay connected to tradition with
                        ease. Download now and bring devotion to your fingertips!</p>

                        <div class="row y-gap-20 items-center pt-30 sm:pt-10">
                            <div class="col-auto">
                              <a href="https://apps.apple.com/in/app/33-crores/id6443912970"
                              target="_blank" class="d-flex items-center px-20 py-10 rounded-8 border-white-15 text-white bg-dark-3">
                                    <div class="icon-apple text-24"></div>
                                    <div class="ml-20">
                                        <div class="text-14">Download on the</div>
                                        <div class="text-15 lh-1 fw-500">Apple Store</div>
                                    </div>
                                  </a>
                            </div>
          
                            <div class="col-auto" >
                                <a href="https://play.google.com/store/apps/details?id=com.thirtythreecroresapp&hl=en"
                                    target="_blank"
                                    class="d-flex items-center px-20 py-10 rounded-8 border-white-15 text-white bg-dark-3">
                                    <div class="icon-play-market text-24"></div>
                                    <div class="ml-20">
          
                                        <div class="text-14">Get in on</div>
                                        <div class="text-15 lh-1 fw-500">Google Play</div>
          
                                    </div>
                                </a>
                            </div>
                        </div>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="500">
                    <img src="{{ asset('images/download.png') }}" alt="image">
                </div>
            </div>
        </div>
    </section>

  

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/counterup2@1.0.4/dist/index.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.counter').counterUp({
                delay: 20, // increased delay
                time: 2000 // increased time
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const readMoreToggle = document.querySelector('.read-more-toggle');
            const shortDescription = document.querySelector('.short-description');
            const fullDescription = document.querySelector('.full-description');

            readMoreToggle.addEventListener('click', function() {
                if (fullDescription.style.display === 'none') {
                    fullDescription.style.display = 'block';
                    readMoreToggle.textContent = 'Read less';
                } else {
                    fullDescription.style.display = 'none';
                    readMoreToggle.textContent = 'Read more';
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Owl Carousel for Home Banner
            $('#homeBannerCarousel').owlCarousel({
                loop: true, // Enable looping
                margin: 10, // Add margin between slides
                nav: false, // Enable next/prev buttons
                autoplay: true, // Enable auto slide
                autoplayTimeout: 2000, // Auto slide timeout (5 seconds)
                autoplayHoverPause: true, // Pause on hover
                items: 1, // Display one item per slide
                dots: false, // Enable dots navigation
                animateOut: 'fadeOut', // Add fade out effect when transitioning between slides
                animateIn: 'fadeIn' // Add fade in effect for incoming slides
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const timers = document.querySelectorAll('[id^="timer-"]');

            timers.forEach(timer => {
                const endTime = new Date(timer.getAttribute('data-endtime')).getTime();

                function updateCountdown() {
                    const now = new Date().getTime();
                    const timeLeft = endTime - now;

                    if (timeLeft <= 0) {
                        timer.innerHTML = "Expired";
                        return;
                    }

                    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                    timer.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        });
    </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap 5 -->

<script>
  // Open the Pause Modal and set the date range
function openPauseModal(startDate, endDate, newDate) {
const startDateField = document.getElementById('pause_start_date');
const endDateField = document.getElementById('pause_end_date');
const subscriptionStartText = document.getElementById('subscriptionStart');
const subscriptionEndText = document.getElementById('subscriptionEnd');

// Set the date range text
subscriptionStartText.textContent = startDate;

// Check if new_date is available and set the end date accordingly
const adjustedEndDate = newDate || endDate;
subscriptionEndText.textContent = adjustedEndDate;

// Set the min and max attributes for the date fields
startDateField.setAttribute('min', startDate);
endDateField.setAttribute('max', adjustedEndDate);
endDateField.setAttribute('min', startDate);
endDateField.setAttribute('max', adjustedEndDate);

// Open the modal using Bootstrap
new bootstrap.Modal(document.getElementById('pauseModal')).show();
}

    // Open the Resume Modal
    function openResumeModal(pauseStartDate, pauseEndDate,newDate) {
        const resumeDateField = document.getElementById('resume_date');

        const adjustedEndDate = newDate || pauseEndDate;

        // Set the min and max for the resume date field
        resumeDateField.setAttribute('min', pauseStartDate);
        resumeDateField.setAttribute('max', adjustedEndDate);

        // Set the default date to the pause start date
        resumeDateField.value = pauseStartDate;

        // Open the modal using Bootstrap
        new bootstrap.Modal(document.getElementById('resumeModal')).show();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000
    });
</script>

<script>
  // Function to set the min attribute of the Pause End Date
  document.getElementById('pause_start_date').addEventListener('change', function () {
      let startDate = this.value;
      document.getElementById('pause_end_date').setAttribute('min', startDate);
  });
</script>

@endif
@endsection
