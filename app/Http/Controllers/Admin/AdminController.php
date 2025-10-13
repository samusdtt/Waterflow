<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Product;
use App\Models\JarRecord;
use App\Models\DailyAccount;
use App\Models\StaffAttendance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get statistics
        $stats = $this->getDashboardStats();
        
        // Get recent orders
        $recentOrders = Order::with(['client', 'supplier', 'orderItems.product'])
            ->latest()
            ->limit(10)
            ->get();

        // Get suppliers
        $suppliers = Supplier::withCount(['orders', 'users'])
            ->latest()
            ->limit(5)
            ->get();

        // Get low stock products
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('min_stock_level'))
            ->with('supplier')
            ->get();

        // Get subscription alerts
        $subscriptionAlerts = Supplier::where('subscription_end_date', '<=', now()->addDays(7))
            ->where('subscription_status', 'active')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'suppliers',
            'lowStockProducts',
            'subscriptionAlerts'
        ));
    }

    /**
     * Show daily order database
     */
    public function dailyOrders(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');
        $location = $request->get('location');

        $query = Order::with(['client', 'supplier', 'orderItems.product', 'staff'])
            ->whereDate('created_at', $date);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($location) {
            $query->where('delivery_address', 'like', "%{$location}%");
        }

        $orders = $query->latest()->get();
        $suppliers = Supplier::where('is_active', true)->get();

        // Group orders by location
        $ordersByLocation = $orders->groupBy(function ($order) {
            $address = strtolower($order->delivery_address);
            if (str_contains($address, 'mall')) return 'Mall';
            if (str_contains($address, 'flat')) return 'Flats';
            if (str_contains($address, 'site')) return 'Site';
            if (str_contains($address, 'store')) return 'Store';
            return 'Other';
        });

        return view('admin.daily-orders', compact(
            'orders',
            'ordersByLocation',
            'suppliers',
            'date',
            'supplierId',
            'location'
        ));
    }

    /**
     * Show client-wise data
     */
    public function clientData(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        $clientId = $request->get('client_id');

        $query = User::where('role', 'client')
            ->with(['orders.orderItems.product', 'supplier']);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($clientId) {
            $query->where('id', $clientId);
        }

        $clients = $query->withCount('orders')
            ->latest()
            ->paginate(20);

        $suppliers = Supplier::where('is_active', true)->get();

        return view('admin.client-data', compact('clients', 'suppliers', 'supplierId', 'clientId'));
    }

    /**
     * Show staff management
     */
    public function staffManagement(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        $staffId = $request->get('staff_id');

        $query = User::where('role', 'staff')
            ->with(['supplier', 'attendance' => function ($q) {
                $q->where('attendance_date', today());
            }]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($staffId) {
            $query->where('id', $staffId);
        }

        $staff = $query->withCount('assignedOrders')
            ->latest()
            ->paginate(20);

        $suppliers = Supplier::where('is_active', true)->get();

        return view('admin.staff-management', compact('staff', 'suppliers', 'supplierId', 'staffId'));
    }

    /**
     * Show daily accounts
     */
    public function dailyAccounts(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        $query = DailyAccount::with('supplier')
            ->where('account_date', $date);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $accounts = $query->get();
        $suppliers = Supplier::where('is_active', true)->get();

        // Calculate totals
        $totalIncome = $accounts->sum('total_income');
        $totalExpenses = $accounts->sum('total_expenses');
        $netProfit = $accounts->sum('net_profit');

        return view('admin.daily-accounts', compact(
            'accounts',
            'suppliers',
            'date',
            'supplierId',
            'totalIncome',
            'totalExpenses',
            'netProfit'
        ));
    }

    /**
     * Show jar records
     */
    public function jarRecords(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $supplierId = $request->get('supplier_id');

        $query = JarRecord::with('supplier')
            ->where('record_date', $date);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $jarRecords = $query->get();
        $suppliers = Supplier::where('is_active', true)->get();

        // Calculate totals
        $totalRefilling = $jarRecords->sum('total_refilling');
        $totalEmpty = $jarRecords->sum('empty_jars');
        $totalOnboard = $jarRecords->sum('onboard_jars');

        return view('admin.jar-records', compact(
            'jarRecords',
            'suppliers',
            'date',
            'supplierId',
            'totalRefilling',
            'totalEmpty',
            'totalOnboard'
        ));
    }

    /**
     * Create/Update daily account
     */
    public function updateDailyAccount(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'account_date' => 'required|date',
            'total_income' => 'required|numeric|min:0',
            'total_expenses' => 'required|numeric|min:0',
            'income_notes' => 'nullable|string|max:1000',
            'expense_notes' => 'nullable|string|max:1000',
        ]);

        $netProfit = $request->total_income - $request->total_expenses;

        DailyAccount::updateOrCreate(
            [
                'supplier_id' => $request->supplier_id,
                'account_date' => $request->account_date,
            ],
            [
                'total_income' => $request->total_income,
                'total_expenses' => $request->total_expenses,
                'net_profit' => $netProfit,
                'income_notes' => $request->income_notes,
                'expense_notes' => $request->expense_notes,
            ]
        );

        return back()->with('success', 'Daily account updated successfully!');
    }

    /**
     * Create/Update jar record
     */
    public function updateJarRecord(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'record_date' => 'required|date',
            'total_refilling' => 'required|integer|min:0',
            'empty_jars' => 'required|integer|min:0',
            'onboard_jars' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        JarRecord::updateOrCreate(
            [
                'supplier_id' => $request->supplier_id,
                'record_date' => $request->record_date,
            ],
            $request->only([
                'total_refilling',
                'empty_jars',
                'onboard_jars',
                'notes'
            ])
        );

        return back()->with('success', 'Jar record updated successfully!');
    }

    /**
     * Mark payment as received
     */
    public function markPaymentReceived(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,online',
            'notes' => 'nullable|string|max:1000',
        ]);

        $payment = Payment::create([
            'payment_id' => Payment::generatePaymentId(),
            'order_id' => $order->id,
            'supplier_id' => $order->supplier_id,
            'user_id' => $order->client_id,
            'type' => 'order_payment',
            'method' => $request->payment_method,
            'status' => 'completed',
            'amount' => $request->amount,
            'notes' => $request->notes,
            'paid_at' => now(),
        ]);

        // Update order payment status
        $order->update([
            'payment_status' => 'paid',
            'paid_amount' => $order->paid_amount + $request->amount,
            'due_amount' => max(0, $order->due_amount - $request->amount),
        ]);

        return back()->with('success', 'Payment marked as received!');
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = today();
        
        return [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'total_orders_today' => Order::whereDate('created_at', $today)->count(),
            'total_revenue_today' => Order::whereDate('created_at', $today)->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'due_payments' => Order::where('payment_status', 'due')->sum('due_amount'),
            'total_clients' => User::where('role', 'client')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
        ];
    }
}