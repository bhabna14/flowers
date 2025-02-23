<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getCurrentOrders(Request $request)
    {
        try {
            if (Auth::guard('users')->check()) {
                $userId = Auth::guard('users')->user()->userid;
            
                // Fetch current orders with the latest subscription for each order_id
                $currentOrders = Order::whereNull('request_id')
                    ->where('user_id', $userId)
                    ->whereHas('subscription', function ($query) {
                        $query->where('status', '!=', 'dead');
                    })
                    ->with([
                        'subscription' => function ($query) {
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
                        $subscription->display_end_date = $subscription->new_date ?? $subscription->end_date;
                        $subscription->remaining_time = now()->diff($subscription->display_end_date);
                    }
            
                    if ($order->flowerProduct) {
                        $order->flowerProduct->product_image_url = $order->flowerProduct->product_image;
                    }
            
                    return $order;
                });
            
                return response()->json([
                    'success' => true,
                    'message' => 'Orders fetched successfully.',
                    'data' => $currentOrders
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated.'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
