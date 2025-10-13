<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is inactive.']);
            }

            // Check supplier access for non-super-admin users
            if (!$user->isSuperAdmin() && $user->supplier_id) {
                if (!$user->supplier || !$user->supplier->is_active) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Supplier account is inactive.']);
                }

                if (!$user->supplier->isSubscriptionActive()) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Supplier subscription is inactive.']);
                }
            }

            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'required|string|max:20',
            'role' => 'required|in:supplier_admin,client',
            'supplier_name' => 'required_if:role,supplier_admin|string|max:255',
            'supplier_email' => 'required_if:role,supplier_admin|email|unique:suppliers,email',
            'supplier_phone' => 'required_if:role,supplier_admin|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $supplierId = null;

        // Create supplier if user is supplier_admin
        if ($request->role === 'supplier_admin') {
            $supplier = Supplier::create([
                'name' => $request->supplier_name,
                'email' => $request->supplier_email,
                'phone' => $request->supplier_phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'subscription_status' => 'inactive',
                'is_active' => false, // Will be activated by super admin
            ]);
            $supplierId = $supplier->id;
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'role' => $request->role,
            'supplier_id' => $supplierId,
        ]);

        Auth::login($user);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(User $user)
    {
        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('admin.dashboard');
            case 'supplier_admin':
                return redirect()->route('supplier.dashboard');
            case 'staff':
                return redirect()->route('staff.dashboard');
            case 'client':
                return redirect()->route('client.dashboard');
            default:
                return redirect()->route('home');
        }
    }
}