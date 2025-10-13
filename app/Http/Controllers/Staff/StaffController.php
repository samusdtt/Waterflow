<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StaffAttendance;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    /**
     * Show staff dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $supplier = $user->supplier;
        
        // Get today's attendance
        $todayAttendance = StaffAttendance::where('staff_id', $user->id)
            ->where('attendance_date', today())
            ->first();

        // Get assigned orders
        $assignedOrders = $user->assignedOrders()
            ->with(['client', 'orderItems.product'])
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->latest()
            ->get();

        // Get recent deliveries
        $recentDeliveries = $user->assignedOrders()
            ->with(['client', 'orderItems.product'])
            ->where('status', 'delivered')
            ->whereDate('delivered_at', '>=', now()->subDays(7))
            ->latest('delivered_at')
            ->limit(5)
            ->get();

        // Get weekly delivery stats
        $weeklyStats = $this->getWeeklyStats($user);

        return view('staff.dashboard', compact(
            'user',
            'supplier',
            'todayAttendance',
            'assignedOrders',
            'recentDeliveries',
            'weeklyStats'
        ));
    }

    /**
     * Show weekly delivery record
     */
    public function weeklyDeliveries()
    {
        $user = Auth::user();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $deliveries = $user->assignedOrders()
            ->with(['client', 'orderItems.product'])
            ->whereBetween('delivered_at', [$startOfWeek, $endOfWeek])
            ->where('status', 'delivered')
            ->latest('delivered_at')
            ->get();

        return view('staff.weekly-deliveries', compact('deliveries', 'startOfWeek', 'endOfWeek'));
    }

    /**
     * Show present/recent orders
     */
    public function orders()
    {
        $user = Auth::user();
        
        $orders = $user->assignedOrders()
            ->with(['client', 'orderItems.product'])
            ->latest()
            ->paginate(10);

        return view('staff.orders', compact('orders'));
    }

    /**
     * Mark order as delivered
     */
    public function markDelivered(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'delivery_notes' => 'nullable|string|max:1000'
        ]);

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'notes' => $request->delivery_notes
        ]);

        return redirect()->route('staff.orders')->with('success', 'Order marked as delivered!');
    }

    /**
     * Request payment verification
     */
    public function requestPaymentVerification(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        if ($order->payment_status !== 'due') {
            return back()->with('error', 'This order does not have due payment.');
        }

        // Create notification for admin
        $order->supplier->notifications()->create([
            'type' => 'dues_request',
            'title' => 'Payment Verification Request',
            'message' => "Staff {$user->name} has requested payment verification for order #{$order->order_number}",
            'data' => [
                'order_id' => $order->id,
                'staff_id' => $user->id,
                'amount' => $order->due_amount
            ]
        ]);

        return back()->with('success', 'Payment verification request sent to admin.');
    }

    /**
     * Show login hours
     */
    public function loginHours()
    {
        $user = Auth::user();
        
        $attendance = StaffAttendance::where('staff_id', $user->id)
            ->latest('attendance_date')
            ->paginate(30);

        return view('staff.login-hours', compact('attendance'));
    }

    /**
     * Clock in
     */
    public function clockIn()
    {
        $user = Auth::user();
        
        // Check if already clocked in today
        $todayAttendance = StaffAttendance::where('staff_id', $user->id)
            ->where('attendance_date', today())
            ->first();

        if ($todayAttendance && $todayAttendance->isLoggedIn()) {
            return back()->with('error', 'You are already clocked in today.');
        }

        if ($todayAttendance) {
            $todayAttendance->update(['login_time' => now()]);
        } else {
            StaffAttendance::create([
                'staff_id' => $user->id,
                'supplier_id' => $user->supplier_id,
                'attendance_date' => today(),
                'login_time' => now(),
            ]);
        }

        return back()->with('success', 'Clocked in successfully!');
    }

    /**
     * Clock out
     */
    public function clockOut()
    {
        $user = Auth::user();
        
        $todayAttendance = StaffAttendance::where('staff_id', $user->id)
            ->where('attendance_date', today())
            ->whereNotNull('login_time')
            ->whereNull('logout_time')
            ->first();

        if (!$todayAttendance) {
            return back()->with('error', 'You are not clocked in today.');
        }

        $totalHours = $todayAttendance->calculateTotalHours();
        
        $todayAttendance->update([
            'logout_time' => now(),
            'total_hours' => $totalHours
        ]);

        return back()->with('success', 'Clocked out successfully!');
    }

    /**
     * Show profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('staff.profile', compact('user'));
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
        ]);

        $user->update($request->only(['name', 'phone']));

        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Get weekly statistics
     */
    private function getWeeklyStats($user)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $deliveries = $user->assignedOrders()
            ->whereBetween('delivered_at', [$startOfWeek, $endOfWeek])
            ->where('status', 'delivered')
            ->count();

        $totalHours = StaffAttendance::where('staff_id', $user->id)
            ->whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->sum('total_hours');

        $totalEarnings = $user->assignedOrders()
            ->whereBetween('delivered_at', [$startOfWeek, $endOfWeek])
            ->where('status', 'delivered')
            ->sum('total_amount');

        return [
            'deliveries' => $deliveries,
            'total_hours' => $totalHours,
            'total_earnings' => $totalEarnings
        ];
    }
}