<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionPauseResumeLog;

use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Bankdetail;
use App\Models\Childrendetail;
use App\Models\Addressdetail;
use App\Models\IdcardDetail;
use App\Models\Poojalist;
use App\Models\UserAddress;
use App\Models\Profile;
use App\Models\Poojadetails;
use App\Models\Locality;
use App\Models\Apartment;
use App\Models\PoojaUnit;
use App\Models\FlowerRequest;
use App\Models\FlowerRequestItem;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log; // Make sure to import the Log facade
use App\Mail\FlowerRequestMail;
use App\Mail\SubscriptionConfirmationMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api;

// use Illuminate\Support\Facades\Log; // Log facade
class FlowerUserBookingController extends Controller
{

  

    public function flower() {
        // Fetch banners from the external API
        $responseBanners = Http::get('https://pandit.33crores.com/api/app-banners');

        // Check if the response is successful and filter based on the 'flower' category
        $banners = $responseBanners->successful() && isset($responseBanners->json()['data'])
            ? collect($responseBanners->json()['data'])->filter(fn($banner) => isset($banner['category']) && strtolower($banner['category']) === 'flower')
            : collect();
           
            
        // Fetch other data for the view
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->take(3)
                        ->get();
        $otherpoojas = Poojalist::where('status', 'active')
                        ->whereNull('pooja_date')
                        ->take(9)
                        ->get();
        $products = FlowerProduct::where('status', 'active')
                        ->where('category', 'Subscription')
                        ->get();
        $customizedpps = FlowerProduct::where('status', 'active')
                        ->where('category', 'Immediateproduct')
                        ->get();
                        $currentOrders = collect();

        if (Auth::guard('users')->check()) {
            $userId = Auth::guard('users')->user()->userid;
        
            // Fetch current orders with latest subscription for each order_id
            $currentOrders = Order::whereNull('request_id')
                ->where('user_id', $userId)
                ->whereHas('subscription', function ($query) {
                    $query->where('status', '!=', 'dead');
                })
                ->with([
                    'subscription' => function ($query) {
                        // Fetch only the latest subscription by order_id
                        $query->orderBy('created_at', 'desc');
                    },
                    'flowerPayments',
                    'user',
                    'flowerProduct',
                    'address.localityDetails',
                    'pauseResumeLogs',
                ])
                ->orderBy('id', 'asc')
                ->get();
        
            // Process the orders and subscriptions
            $currentOrders = $currentOrders->map(function ($order) {
                $subscription = $order->subscription;
                if ($subscription) {
                    // Set the correct end_date
                    $subscription->display_end_date = $subscription->new_date ?? $subscription->end_date;
        
                    // Calculate remaining time
                    $subscription->remaining_time = now()->diff($subscription->display_end_date);
                }
        
                if ($order->flowerProduct) {
                    $order->flowerProduct->product_image_url = $order->flowerProduct->product_image;
                }
        
                return $order;
            });
        }
                        
        return view("user/flower", compact('upcomingPoojas', 'otherpoojas', 'products', 'banners','customizedpps','currentOrders'));
    }
    public function productdetails($slug)
    {
        $product = FlowerProduct::where('slug', $slug)->firstOrFail();
        return view('user.product-details', compact('product'));
    }

    public function show($product_id)
    {
       
        $product = FlowerProduct::where('product_id', $product_id)->firstOrFail();

        $localities = Locality::where('status', 'active')->select('unique_code', 'locality_name', 'pincode')->get();
        $apartments = Apartment::where('status', 'active')->get();
    
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();
       
        return view('user.flower-subscription-checkout', compact('localities','product','addresses','user','apartments'));
    }

    public function cutsomizedcheckout($product_id)
    {
        // dd($product_id);
        // Retrieve the product details by product_id
        // $product = FlowerProduct::findOrFail($product_id);
        $singleflowers = FlowerProduct::where('status', 'active')
                        ->where('category', 'Flower')
                        ->get();
                        
        $Poojaunits = PoojaUnit::where('status', 'active')
        ->get();
        $localities = Locality::where('status', 'active')->select('unique_code', 'locality_name', 'pincode')->get();
        $apartments = Apartment::where('status', 'active')->get();
        $product = FlowerProduct::where('product_id', $product_id)->firstOrFail();
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();
        // $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();

        // Pass the product and subscription details to the view
        return view('user.flower-customized-checkout', compact('Poojaunits','singleflowers','product','addresses','user','localities','apartments'));
    }

     public function processBooking(Request $request)
     {
        $orderId = $request->order_id; // Check if order_id is provided in the request
        $user = Auth::guard('users')->user();
        $productId = $request->product_id;
        $addressId = $request->address_id;
        $suggestion = $request->suggestion;
        $paymentId = $request->razorpay_payment_id; // Razorpay payment ID from frontend
        
        // Log initial request data
        Log::info('Processing booking', [
            'order_id' => $orderId,
            'user_id' => $user->userid,
            'payment_id' => $paymentId,
            'total_price' => $request->price,
            'address_id' => $addressId,
            'suggestion' => $suggestion,
        ]);
        
        try {
            if ($orderId) {
                // Check if order exists in the database
                $order = Order::where('order_id', $orderId)->first();
        
                if ($order) {
                    // Update existing order
                    $order->update([
                        'product_id' => $productId,
                        'user_id' => $user->userid,
                        'quantity' => 1,
                        'total_price' => $request->price,
                        'address_id' => $addressId,
                        'suggestion' => $suggestion,
                    ]);
                    Log::info('Order updated successfully', ['order_id' => $orderId]);
                } else {
                    Log::error('Order ID not found for update', ['order_id' => $orderId]);
                    return back()->with('error', 'Failed to create order');
                }
            } else {
                // Generate new order_id if not provided
                $orderId = 'ORD-' . strtoupper(Str::random(12));
                // Create a new order
                $order = Order::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'user_id' => $user->userid,
                    'quantity' => 1,
                    'total_price' => $request->price,
                    'address_id' => $addressId,
                    'suggestion' => $suggestion,
                ]);
                Log::info('Order created successfully', ['order_id' => $orderId]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing order', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create order');
        }
        
     
         // Initialize Razorpay API
         $razorpayApi = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
     
         try {
             // Fetch the payment details from Razorpay
             $payment = $razorpayApi->payment->fetch($paymentId);
             
             // Log the payment details
             Log::info('Razorpay Payment fetched', [
                 'payment_id' => $paymentId,
                 'payment_status' => $payment->status,
                 'amount' => $payment->amount,
             ]);
     
             // Check if the payment is already captured
             if ($payment->status == 'captured') {
                 Log::info('Payment already captured', ['payment_id' => $paymentId]);
             } else {
                 // If payment is not captured, attempt to capture it
                 $capture = $razorpayApi->payment->fetch($paymentId)->capture(['amount' => $payment->amount]);
                 Log::info('Payment captured manually', [
                     'payment_id' => $paymentId,
                     'captured_status' => $capture->status
                 ]);
             }
         } catch (\Exception $e) {
             Log::error('Failed to capture payment', ['error' => $e->getMessage()]);
             return back()->with('error', 'Failed to capture payment');
         }
     
         // Calculate subscription start and end dates
         $startDate = $request->start_date ? Carbon::parse($request->start_date) : now();
         $duration = $request->duration;
     
         if ($duration == 1) {
             $endDate = $startDate->copy()->addDays(29);
         } else if ($duration == 3) {
             $endDate = $startDate->copy()->addDays(89);
         } else if ($duration == 6) {
             $endDate = $startDate->copy()->addDays(179);
         } else {
             Log::error('Invalid subscription duration', ['duration' => $duration]);
             return back()->with('error', 'Invalid subscription duration');
         }
     
         $subscriptionId = 'SUB-' . strtoupper(Str::random(12));
         $today = now()->format('Y-m-d');
         $status = ($startDate->format('Y-m-d') === $today) ? 'active' : 'pending';
     
         try {
             Subscription::create([
                 'subscription_id' => $subscriptionId,
                 'user_id' => $user->userid,
                 'order_id' => $orderId,
                 'product_id' => $productId,
                 'start_date' => $startDate,
                 'end_date' => $endDate,
                 'is_active' => true,
                 'status' => $status
             ]);
             Log::info('Subscription created successfully', ['subscription_id' => $subscriptionId]);
         } catch (\Exception $e) {
             Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
             return back()->with('error', 'Failed to create subscription');
         }
     
         try {
             FlowerPayment::create([
                 'order_id' => $orderId,
                 'payment_id' => $paymentId,
                 'user_id' => $user->userid,
                 'payment_method' => "Razorpay",
                 'paid_amount' => $request->price,
                 'payment_status' => "paid",
             ]);
             Log::info('Payment recorded successfully', ['payment_id' => $paymentId]);
         } catch (\Exception $e) {
             Log::error('Failed to record payment', ['error' => $e->getMessage()]);
             return back()->with('error', 'Failed to record payment');
         }
     
         // Send email
         $order = Order::with(['flowerProduct', 'user', 'address.localityDetails', 'flowerPayments', 'subscription'])
             ->where('order_id', $orderId)
             ->first();
     
         if (!$order) {
             Log::error('Order not found', ['order_id' => $orderId]);
             return response()->json(['message' => 'Order not found'], 404);
         }
     
         $emails = ['soumyapuhan22@gmail.com'];
     
         try {
             Mail::to($emails)->send(new SubscriptionConfirmationMail($order));
             Log::info('Order confirmation email sent', ['order_id' => $orderId]);
         } catch (\Exception $e) {
             Log::error('Failed to send order details email', ['error' => $e->getMessage()]);
         }
     
         return redirect()->back()->with('success', 'Your booking has been processed successfully');
     }
    //  public function subscriptionHistory()
    //  {
    //      // Get the authenticated user's ID using the 'api' guard
    //      $userId = Auth::guard('users')->user()->userid;
     
    //      // Fetch all orders for the user with related data
    //      $subscriptionsOrder = Order::where('user_id', $userId)
    //          ->with([
    //              'subscription' => function ($query) {
    //                  $query->orderBy('created_at', 'desc'); // Order subscriptions by the latest
    //              },
    //              'flowerPayments',
    //              'user',
    //              'flowerProduct',
    //              'address.localityDetails',
    //              'pauseResumeLogs',
    //          ])
    //          ->orderBy('id', 'desc')
    //          ->get();
     
    //      // Process each order to ensure all related subscriptions are attached
    //      $subscriptionsOrder = $subscriptionsOrder->map(function ($order) {
    //          // Ensure the flower product exists before accessing its image
    //          if ($order->flowerProduct) {
    //              $order->flowerProduct->product_image_url = $order->flowerProduct->product_image;
    //          }
     
    //          // Attach all subscriptions associated with the order
    //          $order->allSubscriptions = $order->subscription; // Get all subscriptions for the order
     
    //          return $order;
    //      });
     
    //      // Pass all orders and their associated subscriptions to the view
    //      return view('user.subscription-history', compact('subscriptionsOrder'));
    //  }


    public function subscriptionHistory()
{
    // Get the authenticated user's ID using the 'api' guard
    $userId = Auth::guard('users')->user()->userid;

    // Fetch all subscriptions for the user
    $subscriptions = Subscription::where('user_id', $userId)
        ->with([
            'order', // To get associated orders if needed
            'flowerProducts', // If you need product info related to subscriptions
            'pauseResumeLog',
            'flowerPayments',
            'users',
            
        ])
        ->orderBy('created_at', 'desc') // Order by latest subscription
        ->get();

        $subscriptions = $subscriptions->map(function ($subscription) {
            if ($subscription->flowerProducts) {
                // Add the image URL to the flower product
                $subscription->flowerProducts->product_image_url = $subscription->flowerProducts->product_image;
            }
    
            return $subscription;
        });

    // Pass all subscriptions to the view
    return view('user.subscription-history', compact('subscriptions'));
}

public function viewSubscriptionOrderDetails($subscription_id, $order_id)
{
    // Fetch the order details using the order_id
    $order = Subscription::where('id', $subscription_id)
        ->with([
            'order' => function($query) use ($order_id) {
                $query->where('order_id', $order_id);  // Filter orders by the provided order_id
            },   
            'flowerPayments',
            'users',
            'flowerProducts',
            'pauseResumeLog',
            'order.address.localityDetails'
            ])
        ->firstOrFail(); // Ensure the order exists or fail
    
    // Add the product image URL
    if ($order->flowerProducts) {
        $order->flowerProducts->product_image_url = asset($order->flowerProducts->product_image);
    }
    // Pass the order to the view
    return view('user.view-subscription-details', compact('order'));
}

    public function requestedorderhistory(){
        $userId = Auth::guard('users')->user()->userid;
        $requestedOrders = FlowerRequest::where('user_id', $userId)
        ->with([
            'order' => function ($query) {
                $query->with('flowerPayments');
            },
            'flowerProduct',
            'user',
            'address.localityDetails',
            'flowerRequestItems' 
        ])
        ->orderBy('id', 'desc')
        ->get();
        
        return view('user.requested-order-history', compact('requestedOrders'));

    }
    public function requestedOrderDetails($id)
{
    $userId = Auth::guard('users')->user()->userid;
    
    // Fetch the requested order by ID and include its relationships
    $requestedOrder = FlowerRequest::where('id', $id)
        ->where('user_id', $userId)
        ->with([
            'order.flowerPayments',
            'flowerProduct',
            'user',
            'address.localityDetails',
            'flowerRequestItems'
        ])
        ->firstOrFail();

    return view('user.view-requested-order-details', compact('requestedOrder'));
}


    public function userflowerdashboard(){
        return view('user.user-flower-dashboard');
    }

    // customized order
    public function customizedstore(Request $request)
    {
       
        $user = Auth::guard('users')->user();

        // Generate the request ID
        $requestId = 'REQ-' . strtoupper(Str::random(12));

        // Create the flower request and store the request ID
        $flowerRequest = FlowerRequest::create([
            'request_id' => $requestId,
            'product_id' => $request->product_id,
            'user_id' => $user->userid,
            'address_id' => $request->address_id,
            'description' => $request->description,
            'suggestion' => $request->suggestion,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending'
        ]);

        // Loop through flower names, units, and quantities to create FlowerRequestItem entries
        foreach ($request->item as $index => $flowerName) {
            FlowerRequestItem::create([
                'flower_request_id' => $requestId,
                'flower_name' => $flowerName,
                'flower_unit' => $request->unit[$index],
                'flower_quantity' => $request->quantity[$index],
            ]);
        }
        try {
            // Log the alert for a new order
            // Log::info('New order created successfully.', ['request_id' => $requestId]);
        
            // Array of email addresses to send the email
            $emails = [
                'bhabana.samantara@33crores.com',
                'pankaj.sial@33crores.com',
                'basudha@33crores.com',
                'priya@33crores.com',
                'starleen@33crores.com',
            ];
        
            // Log before attempting to send the email
            // Log::info('Attempting to send email to multiple recipients.', ['emails' => $emails]);
        
            // Send the email to all recipients
            Mail::to($emails)->send(new FlowerRequestMail($flowerRequest));
        
            // // Log success
            // Log::info('Email sent successfully to multiple recipients.', [
            //     'request_id' => $requestId,
            //     'user_id' => $user->userid,
            // ]);
        
        } catch (\Exception $e) {
            // Log the error with details
            Log::error('Failed to send email.', [
                'request_id' => $requestId,
                'user_id' => $user->userid ?? 'N/A',
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
        // Return success message using SweetAlert and redirect
        // return redirect()->route('flower.history')->with('success', 'Flower request created successfully!');
        return redirect()->back()->with('message', 'Flower request created successfully!');

    }

    public function RequestpaymentCallback(Request $request)
    {
        try {
            $request->validate([
                'razorpay_payment_id' => 'required',
                // 'razorpay_order_id' => 'required',
                'request_id' => 'required',
            ]);
    
            $order = Order::where('request_id', $request->request_id)->firstOrFail();
    
            // Save payment details
            FlowerPayment::create([
                'order_id' => $order->order_id,
                'payment_id' => $request->razorpay_payment_id,
                'user_id' => $order->user_id,
                'payment_method' => 'Razorpay',
                'paid_amount' => $order->total_price,
                'payment_status' => 'paid',
            ]);
    
            // Update FlowerRequest status
            $flowerRequest = FlowerRequest::where('request_id', $request->request_id)->firstOrFail();
            if ($flowerRequest->status === 'approved') {
                $flowerRequest->status = 'paid';
                $flowerRequest->save();
            }
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Payment error: ' . $e->getMessage());
    
            return response()->json(['success' => false, 'message' => 'Payment processing failed.']);
        }
    }


    public function pause(Request $request, $order_id)
    {
        try {
            // Find the subscription by order_id
            $subscription = Subscription::where('order_id', $order_id)->where('status','active')->firstOrFail();
            
            // Validate input dates
            $pauseStartDate = Carbon::parse($request->pause_start_date);
            $pauseEndDate = Carbon::parse($request->pause_end_date);
            $pausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1; // Include both dates

            // Get the most recent new_end_date or default to the original end_date
            $lastNewEndDate = SubscriptionPauseResumeLog::where('subscription_id', $subscription->subscription_id)
                ->orderBy('id', 'desc')
                ->value('new_end_date');

            // Use the most recent new_end_date for recalculating the new end date
            $currentEndDate = $lastNewEndDate ? Carbon::parse($lastNewEndDate) : Carbon::parse($subscription->end_date);

            // Calculate the new end date by adding paused days
            $newEndDate = $currentEndDate->addDays($pausedDays);

            // Update the subscription status and new date field
            $subscription->pause_start_date = $pauseStartDate;
            $subscription->pause_end_date = $pauseEndDate;
            $subscription->new_date = $newEndDate; // Update with recalculated end date
            $subscription->is_active = true;

            // Save the changes
            $subscription->save();

            // Log the pause action
            SubscriptionPauseResumeLog::create([
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $order_id,
                'action' => 'paused',
                'pause_start_date' => $pauseStartDate,
                'pause_end_date' => $pauseEndDate,
                'paused_days' => $pausedDays,
                'new_end_date' => $newEndDate,
            ]);

            // Log the creation of the pause resume log
            Log::info('Pause resume log created successfully');

            return redirect()->route('subscription.history')->with('success', 'Subscription paused successfully.');

   
        } catch (\Exception $e) {
            // Log any errors that occur during the process
            Log::error('Error pausing subscription', [
                'order_id' => $order_id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'An error occurred while pausing the subscription.');

          
        }
    }


    public function resume(Request $request, $order_id)
    {
        try {
            Log::info('Resume subscription request received.', ['order_id' => $order_id, 'request_data' => $request->all()]);
    
            // Find the subscription by order_id
            $subscription = Subscription::where('order_id', $order_id)->where('status','paused')->firstOrFail();
            Log::info('Subscription found.', ['subscription' => $subscription]);
    
            // Validate that the subscription is currently paused
            if ($subscription->status !== 'paused') {
                Log::warning('Subscription is not in a paused state.', ['subscription_status' => $subscription->status]);
               
                return redirect()->back()->with('success', 'Subscription is not in a paused state.');

                
            }
    
            // Parse the dates
            $resumeDate = Carbon::parse($request->resume_date);
            $pauseStartDate = Carbon::parse($subscription->pause_start_date);
            $pauseEndDate = Carbon::parse($subscription->pause_end_date);
            $currentEndDate = $subscription->new_date ? Carbon::parse($subscription->new_date) : Carbon::parse($subscription->end_date);
    
            Log::info('Parsed dates.', [
                'resume_date' => $resumeDate,
                'pause_start_date' => $pauseStartDate,
                'pause_end_date' => $pauseEndDate,
                'current_end_date' => $currentEndDate
            ]);
    
            // Ensure the resume date is within the pause period
            if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
                Log::warning('Resume date is outside the pause period.', ['resume_date' => $resumeDate]);
              

                return redirect()->back()->with('success', 'Resume date must be within the pause period');

            }
    
            // Calculate the days actually paused until the resume date
            $actualPausedDays = $resumeDate->diffInDays($pauseStartDate);
    
            // Calculate total planned paused days
            $totalPausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1;
    
            // Calculate the remaining paused days to adjust if resuming early
            $remainingPausedDays = $totalPausedDays - $actualPausedDays;
    
            Log::info('Pause days calculated.', [
                'actual_paused_days' => $actualPausedDays,
                'total_paused_days' => $totalPausedDays,
                'remaining_paused_days' => $remainingPausedDays
            ]);
    
            // Adjust the new end date by subtracting the remaining paused days if necessary
            if ($remainingPausedDays > 0) {
                $newEndDate = $currentEndDate->subDays($actualPausedDays);
            } else {
                $newEndDate = $currentEndDate;
            }
    
            // Update the subscription status and clear pause dates
            $subscription->status = 'active';
            $subscription->pause_start_date = null;
            $subscription->pause_end_date = null;
            $subscription->new_date = $newEndDate;
            $subscription->save();
    
            Log::info('Subscription resumed.', ['subscription' => $subscription]);
    
            // Log the resume action
            SubscriptionPauseResumeLog::create([
                'subscription_id' => $subscription->subscription_id,
                'order_id' => $order_id,
                'action' => 'resumed',
                'resume_date' => $resumeDate,
                'pause_start_date' => $pauseStartDate,
                'new_end_date' => $newEndDate,
                'paused_days' => $actualPausedDays,
            ]);
    
            Log::info('Resume log created.', ['order_id' => $order_id]);

            return redirect()->route('subscription.history')->with('success', 'Subscription resumed successfully.');


        } catch (\Exception $e) {

            Log::error('Error while resuming subscription.', ['order_id' => $order_id, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'An error occurred while pausing the subscription.');

        }
    }
    
    public function pausePage($order_id)
    {

        $order = Subscription::where('order_id', $order_id)->firstOrFail();

        return view('user.pause-resume' , [
            'order' => $order,
            'action' => 'pause',
        ]);

    }

    public function resumePage($order_id)
    {
        $order = Subscription::where('order_id', $order_id)->firstOrFail();

        return view('user.pause-resume', [
            'order' => $order,
            'action' => 'resume',
        ]);
    }

    
}