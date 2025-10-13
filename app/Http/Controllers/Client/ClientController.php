<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Show client dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $supplier = $user->supplier;
        
        // Get recent orders
        $recentOrders = $user->orders()
            ->with(['orderItems.product', 'staff'])
            ->latest()
            ->limit(5)
            ->get();

        // Get order statistics
        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $deliveredOrders = $user->orders()->where('status', 'delivered')->count();
        $dueAmount = $user->orders()->where('payment_status', 'due')->sum('due_amount');

        return view('client.dashboard', compact(
            'user',
            'supplier',
            'recentOrders',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'dueAmount'
        ));
    }

    /**
     * Show products for ordering
     */
    public function products()
    {
        $user = Auth::user();
        $supplier = $user->supplier;
        
        $products = Product::where('supplier_id', $supplier->id)
            ->where('is_active', true)
            ->get()
            ->groupBy('type');

        return view('client.products', compact('products', 'supplier'));
    }

    /**
     * Show cart
     */
    public function cart()
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);
        
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'total' => $product->price * $item['quantity']
                ];
                $total += $product->price * $item['quantity'];
            }
        }

        return view('client.cart', compact('cartItems', 'total'));
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'quantity' => $request->quantity,
                'product' => $product
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = session()->get('cart', []);

        if ($request->quantity == 0) {
            unset($cart[$request->product_id]);
        } else {
            $cart[$request->product_id]['quantity'] = $request->quantity;
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        return response()->json(['success' => true]);
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('client.products')->with('error', 'Your cart is empty.');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $itemTotal = $product->price * $item['quantity'];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal
                ];
                $subtotal += $itemTotal;
            }
        }

        $taxAmount = $subtotal * 0.18; // 18% GST
        $total = $subtotal + $taxAmount;

        return view('client.checkout', compact('cartItems', 'subtotal', 'taxAmount', 'total', 'user'));
    }

    /**
     * Process order
     */
    public function processOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,online,due,credit_points',
            'delivery_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('client.products')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = 0;
        $orderItems = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
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

        // Clear cart
        session()->forget('cart');

        // Handle payment based on method
        if ($request->payment_method === 'online') {
            // Redirect to payment gateway
            return redirect()->route('client.payment', $order->id);
        }

        return redirect()->route('client.orders')->with('success', 'Order placed successfully!');
    }

    /**
     * Show order history
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['orderItems.product', 'staff'])
            ->latest()
            ->paginate(10);

        return view('client.orders', compact('orders'));
    }

    /**
     * Show order details
     */
    public function orderDetails(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['orderItems.product', 'staff', 'payments']);
        
        return view('client.order-details', compact('order'));
    }

    /**
     * Show profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('client.profile', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ]);

        $user->update($request->only([
            'name', 'phone', 'address', 'city', 'state', 'pincode'
        ]));

        return redirect()->route('client.profile')->with('success', 'Profile updated successfully!');
    }
}