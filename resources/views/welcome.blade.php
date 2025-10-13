@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-8">Water Management SaaS</h1>
                    <p class="text-xl text-gray-600 mb-8">Complete water delivery management solution with multi-supplier support</p>
                    
                    <div class="grid md:grid-cols-3 gap-8 mb-12">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-blue-900 mb-4">For Clients</h3>
                            <ul class="text-left text-blue-800 space-y-2">
                                <li>• Create and manage orders</li>
                                <li>• Track delivery status</li>
                                <li>• Multiple payment options</li>
                                <li>• Order history and invoices</li>
                            </ul>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-green-900 mb-4">For Staff</h3>
                            <ul class="text-left text-green-800 space-y-2">
                                <li>• Weekly delivery records</li>
                                <li>• Mark orders as delivered</li>
                                <li>• Track login hours</li>
                                <li>• Payment verification requests</li>
                            </ul>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-purple-900 mb-4">For Admins</h3>
                            <ul class="text-left text-purple-800 space-y-2">
                                <li>• Daily order management</li>
                                <li>• Client and staff management</li>
                                <li>• Financial tracking</li>
                                <li>• Jar inventory management</li>
                            </ul>
                        </div>
                    </div>
                    
                    @guest
                        <div class="space-x-4">
                            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 text-lg font-semibold">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 text-lg font-semibold">
                                Register
                            </a>
                        </div>
                    @else
                        <div class="space-x-4">
                            @if(Auth::user()->role === 'client')
                                <a href="{{ route('client.dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 text-lg font-semibold">
                                    Client Dashboard
                                </a>
                            @elseif(Auth::user()->role === 'staff')
                                <a href="{{ route('staff.dashboard') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 text-lg font-semibold">
                                    Staff Dashboard
                                </a>
                            @elseif(Auth::user()->role === 'supplier_admin')
                                <a href="{{ route('supplier.dashboard') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 text-lg font-semibold">
                                    Supplier Dashboard
                                </a>
                            @elseif(Auth::user()->role === 'super_admin')
                                <a href="{{ route('admin.dashboard') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 text-lg font-semibold">
                                    Admin Dashboard
                                </a>
                            @endif
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection