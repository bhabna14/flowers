<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\FlowerRequest;
use App\Models\FlowerPayment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\SubscriptionPauseResumeLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionPaused;
use App\Mail\SubscriptionResumed;
use App\Mail\SubscriptionPausedAdmin;
use App\Mail\SubscriptionResumedAdmin;
use App\Mail\SubscriptionPausedUser;
use App\Mail\SubscriptionResumedUser;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FlowerOrderController extends Controller
{
    //
    public function showOrders()
    {
        $orders = Order::whereNull('request_id')
                       ->with(['flowerRequest', 'subscription', 'flowerPayments', 'user','flowerProduct','address.localityDetails'])
                       ->orderBy('id', 'desc')
                       ->get();
                       $activeSubscriptions = Subscription::where('status', 'active')->count();

                       // Paused subscriptions count
                       $pausedSubscriptions = Subscription::where('status', 'paused')->count();
               
                       // Orders requested today
                       $ordersRequestedToday = Subscription::whereDate('created_at', Carbon::today())->count();
                       
        return view('admin.flower-order.manage-flower-order', compact('orders','activeSubscriptions', 'pausedSubscriptions', 'ordersRequestedToday'));
    }
    public function showCustomerDetails($userid)
    {
        // Fetch user details by `userid` instead of `id`
        $user = User::where('userid', $userid)->firstOrFail();
        $addressdata = UserAddress::where('user_id', $userid)
                                ->where('status','active')
                                ->get();
    
        // Fetch user orders based on `userid`
        // $orders = Order::where('user_id', $userid)
        //                ->with(['flowerProduct', 'subscription', 'flowerPayments', 'address'])
        //                ->orderBy('id', 'desc')
        //                ->get();

        $orders = Order::where('user_id', $userid)
    ->whereHas('subscription', function ($query) {
        // This ensures that only orders with a related subscription are included
        $query->whereColumn('orders.order_id', 'subscriptions.order_id');
    })
    ->with(['flowerProduct', 'subscription', 'flowerPayments', 'address.localityDetails'])
    ->orderBy('id', 'desc')
    ->get();


    $pendingRequests =  FlowerRequest::where('user_id', $userid)
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
    
    // Step 2: For each flower request, check if an associated order exists
    foreach ($pendingRequests as $request) {
        $request->order = Order::where('request_id', $request->request_id)
            ->with('flowerPayments')
            ->first();
    }
    
    // Now $flowerRequests will have the associated order data if it exists
    
    

        $totalOrders = Order::where('user_id', $userid)->count();
        $ongoingOrders = Order::where('user_id', $userid)
                          ->whereHas('subscription', function ($query) {
                              $query->where('status', 'active'); // Adjust status value as needed
                          })
                          ->count();

    // Total spend
    $totalSpend = Order::where('user_id', $userid)->sum('total_price'); // Adjust column name if necessary

  
        // Return the view with user and orders data
        return view('admin.flower-order.show-customer-details', compact('user','addressdata','pendingRequests', 'orders','totalOrders', 'ongoingOrders', 'totalSpend'));
    }
    

    
    public function show($id)
    {
        $order = Order::with(['flowerRequest', 'subscription', 'flowerPayments', 'user', 'flowerProduct', 'address', 'pauseResumeLogs'])->findOrFail($id);

    
    
        return view('admin.flower-request.show-order-details', compact('order'));
    }
    


public function showActiveSubscriptions()
{
    $activeSubscriptions = Subscription::where('status', 'active')
        ->with(['relatedOrder.flowerRequest', 'relatedOrder.flowerPayments', 'relatedOrder.user', 'relatedOrder.flowerProduct', 'relatedOrder.address'])
        ->get();

    return view('admin.flower-order.manage-active-subscriptions', compact('activeSubscriptions'));
}
public function showPausedSubscriptions()
{
    $pausedSubscriptions = Subscription::where('status', 'paused')
        ->with(['relatedOrder.flowerRequest', 'relatedOrder.flowerPayments', 'relatedOrder.user', 'relatedOrder.flowerProduct', 'relatedOrder.address'])
        ->get();

    return view('admin.flower-order.manage-paused-subscriptions', compact('pausedSubscriptions'));
}

public function showOrdersToday()
{
    $today = \Carbon\Carbon::today();
    $ordersRequestedToday = Subscription::whereDate('start_date', $today)
        ->with(['order.flowerPayments', 'order.user', 'order.flowerProduct', 'order.address'])
        ->get();

    return view('admin.flower-order.manage-today-requestorder', compact('ordersRequestedToday'));
}



public function pause(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

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
        $subscription->update([
            'status' => 'paused',
            'pause_start_date' => $pauseStartDate,
            'pause_end_date' => $pauseEndDate,
            'new_date' => $newEndDate,
        ]);

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

        return redirect()->back()->with('success', 'Successfully paused subscription.');

    } catch (\Exception $e) {
        // Log any errors that occur during the process
        Log::error('Error pausing subscription', [
            'order_id' => $order_id,
            'error_message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to pause subscription.');
    }
}

public function resume(Request $request, $order_id)
{
    try {
        // Find the subscription by order_id
        $subscription = Subscription::where('order_id', $order_id)->firstOrFail();

        // Validate that the subscription is currently paused
        if ($subscription->status !== 'paused') {
            return redirect()->back()->with('error', 'Subscription is not in a paused state.');
        }

        // Parse the dates
        $resumeDate = Carbon::parse($request->resume_date);
        $pauseStartDate = Carbon::parse($subscription->pause_start_date);
        $pauseEndDate = Carbon::parse($subscription->pause_end_date);
        $currentEndDate = $subscription->new_date ? Carbon::parse($subscription->new_date) : Carbon::parse($subscription->end_date);

        // Ensure the resume date is within the pause period
        if ($resumeDate->lt($pauseStartDate) || $resumeDate->gt($pauseEndDate)) {
            return redirect()->back()->with('error', 'Resume date must be within the pause period.');
        }

        // Calculate the days actually paused until the resume date
        $actualPausedDays = $resumeDate->diffInDays($pauseStartDate) + 1;

        // Adjust the new end date by subtracting the paused days
        $newEndDate = $currentEndDate->subDays($actualPausedDays);

        // Update the subscription
        $subscription->update([
            'status' => 'active',
            'pause_start_date' => null,
            'pause_end_date' => null,
            'new_date' => $newEndDate,
        ]);

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

        return redirect()->back()->with('success', 'Successfully resumed subscription.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to resume subscription.');
    }
}

}
