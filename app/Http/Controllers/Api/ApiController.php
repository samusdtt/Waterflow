<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Get user profile
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load('supplier');
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'name', 'phone', 'address', 'city', 'state', 'pincode'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Get products for client
     */
    public function products()
    {
        $user = Auth::user();
        
        if (!$user->supplier_id) {
            return response()->json([
                'success' => false,
                'message' => 'No supplier access'
            ], 403);
        }

        $products = Product::where('supplier_id', $user->supplier_id)
            ->where('is_active', true)
            ->get()
            ->groupBy('type');

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Create order
     */
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,online,due,credit_points',
            'delivery_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate totals
        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->supplier_id === $user->supplier_id) {
                $itemTotal = $product->price * $item['quantity'];
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $itemTotal
                ];
                $subtotal += $itemTotal;
            }
        }

        if (empty($orderItems)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid items found'
            ], 400);
        }

        $taxAmount = $subtotal * 0.18;
        $total = $subtotal + $taxAmount;

        // Create order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'supplier_id' => $user->supplier_id,
            'client_id' => $user->id,
            'status' => 'pending',
            'payment_status' => $request->payment_method === 'due' ? 'due' : 'pending',
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'due_amount' => $request->payment_method === 'due' ? $total : 0,
            'delivery_address' => $request->delivery_address,
            'notes' => $request->notes,
        ]);

        // Create order items
        foreach ($orderItems as $item) {
            $order->orderItems()->create($item);
        }

        $order->load(['orderItems.product', 'supplier']);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ]);
    }

    /**
     * Get orders for client
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status');
        $limit = $request->get('limit', 20);

        $query = $user->orders()->with(['orderItems.product', 'staff', 'supplier']);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get order details
     */
    public function orderDetails(Order $order)
    {
        $user = Auth::user();
        
        if ($order->client_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $order->load(['orderItems.product', 'staff', 'supplier', 'payments']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Get staff orders
     */
    public function staffOrders(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $status = $request->get('status');
        $limit = $request->get('limit', 20);

        $query = $user->assignedOrders()->with(['client', 'orderItems.product', 'supplier']);

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Mark order as delivered (Staff)
     */
    public function markDelivered(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isStaff() || $order->staff_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'delivery_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'notes' => $request->delivery_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order marked as delivered',
            'data' => $order
        ]);
    }

    /**
     * Clock in (Staff)
     */
    public function clockIn()
    {
        $user = Auth::user();
        
        if (!$user->isStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Check if already clocked in today
        $todayAttendance = \App\Models\StaffAttendance::where('staff_id', $user->id)
            ->where('attendance_date', today())
            ->first();

        if ($todayAttendance && $todayAttendance->isLoggedIn()) {
            return response()->json([
                'success' => false,
                'message' => 'Already clocked in today'
            ], 400);
        }

        if ($todayAttendance) {
            $todayAttendance->update(['login_time' => now()]);
        } else {
            \App\Models\StaffAttendance::create([
                'staff_id' => $user->id,
                'supplier_id' => $user->supplier_id,
                'attendance_date' => today(),
                'login_time' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Clocked in successfully'
        ]);
    }

    /**
     * Clock out (Staff)
     */
    public function clockOut()
    {
        $user = Auth::user();
        
        if (!$user->isStaff()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $todayAttendance = \App\Models\StaffAttendance::where('staff_id', $user->id)
            ->where('attendance_date', today())
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
            ->first();

        if (!$todayAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Not clocked in today'
            ], 400);
        }

        $totalHours = $todayAttendance->calculateTotalHours();
        
        $todayAttendance->update([
            'logout_time' => now(),
            'total_hours' => $totalHours
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully',
            'data' => [
                'total_hours' => $totalHours
            ]
        ]);
    }

    /**
     * Get notifications
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 20);

        $notifications = $user->notifications()
            ->latest()
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(\App\Models\Notification $notification)
    {
        $user = Auth::user();
        
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
}