@extends('user.layouts.front-flower')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

<style>
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
        text-decoration: line-through; /* Strikethrough for the MRP */
        color: #aaa; /* Light grey for the crossed-out MRP */
    }
    
    /* 
    .product-card:hover {
        transform: translateY(-10px) scale(1.03);
    } */
    
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
    
    .product-image-container:hover .product-image1 {
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
        font-size: 16px;
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
    .flower-package-heading h1{
      color: #000;
      text-align: center;
      margin-bottom: 38px;
      letter-spacing: 1px;
    }
    .breadcrumb {
    display: flex; /* Align items horizontally */
    flex-wrap: wrap; /* Prevent overflow */
    background-color: transparent; /* No background */
    padding: 10px 0; /* Padding around breadcrumb */
    margin-bottom: 20px; /* Spacing below breadcrumb */
    font-size: 14px; /* Font size for readability */
    list-style: none; /* Remove default bullet styling */
}

.breadcrumb > .breadcrumb-item {
    display: inline; /* Keep items inline */
}

.breadcrumb > .breadcrumb-item + .breadcrumb-item::before {
    content: "/"; /* Separator */
    padding: 0 5px; /* Space around separator */
    color: #6c757d; /* Muted color for separator */
}

.breadcrumb-item a {
    color: #007bff; /* Link color */
    text-decoration: none; /* No underline on links */
    transition: color 0.3s ease; /* Smooth hover effect */
}

.breadcrumb-item a:hover {
    color: #0056b3; /* Darker blue on hover */
}

.breadcrumb-item.active {
    color: #000; /* Black for active item */
    font-weight: bold; /* Bold text for active item */
}

    </style>
@endsection

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
</div>

<div class="container mt-5 mb-5 pb-50 pt-10">
    <div class="row">
        <!-- Product Image Section -->
        <div class="col-md-5">
            <div class="product-image-container shadow-sm" style="    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    height: 292px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e7dfdf;
    padding: 30px 0px;">
                <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}" class="img-fluid product-image1" style="max-height: 100%; max-width: 100%; object-fit: cover; border-radius:5px;">
            </div>
        </div>
        

        <!-- Product Details Section -->
        <div class="col-md-7">
            <h1 class="product-title">{{ $product->name }}</h1>
            <p class="text-muted">Category: <span class="fw-bold">{{ $product->category ?? 'N/A' }}</span></p>
            
            <!-- Pricing Section -->
            <div class="product-price mb-4">
                <span class="text-decoration-line-through text-muted fs-6">₹ {{ number_format($product->mrp, 2) }}</span>
                <span class="fw-bold fs-3 ms-3 text-success">₹ {{ number_format($product->price, 2) }}</span>
                <p class="savings">Save ₹{{ number_format($product->mrp - $product->price, 2) }}</p>
            </div>

            <!-- Description Section -->
            <p class="product-description fs-5">{{ $product->description }}</p>

            <!-- Highlights -->
            @if($product->details)
            <div class="product-highlights mb-4">
                <h5>Highlights</h5>
                <ul class="list-unstyled">
                    @foreach(explode("\n", $product->details) as $detail)
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $detail }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Order Button -->
            @if(Auth::guard('users')->check())
                <a href="{{ route('checkout', ['product_id' => $product->product_id]) }}" class="btn btn-gradient w-100 mt-2">
                    Order Now
                </a>
            @else
                <a href="{{ route('userlogin', ['referer' => urlencode(route('checkout', ['product_id' => $product->product_id]))]) }}" class="btn btn-gradient w-100 mt-2">
                    Order Now
                </a>
            @endif


        </div>
    </div>

</div>
@endsection
