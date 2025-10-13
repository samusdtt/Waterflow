@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Available Products</h1>
                        <p class="text-gray-600">Order from {{ $supplier->name }}</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('client.cart') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            Cart (<span id="cart-count">0</span>)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products by Type -->
        @foreach($products as $type => $typeProducts)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 capitalize">{{ $type }} Products</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($typeProducts as $product)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                                    <span class="text-sm text-gray-500">{{ $product->brand }}</span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">{{ $product->size }}</p>
                                
                                @if($product->description)
                                    <p class="text-sm text-gray-500 mb-3">{{ $product->description }}</p>
                                @endif
                                
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-2xl font-bold text-green-600">{{ $product->formatted_price }}</span>
                                    <span class="text-sm text-gray-500">Stock: {{ $product->stock_quantity }}</span>
                                </div>
                                
                                @if($product->isOutOfStock())
                                    <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <input type="number" 
                                               id="quantity-{{ $product->id }}" 
                                               min="1" 
                                               max="{{ $product->stock_quantity }}" 
                                               value="1" 
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-center">
                                        <button onclick="addToCart({{ $product->id }})" 
                                                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                                            Add to Cart
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        @if($products->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products available</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for new products.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Add to cart functionality
    function addToCart(productId) {
        const quantity = document.getElementById('quantity-' + productId).value;
        
        fetch('{{ route("client.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: parseInt(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                document.getElementById('cart-count').textContent = data.cart_count;
                
                // Show success message
                showNotification('Product added to cart!', 'success');
            } else {
                showNotification('Error adding product to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding product to cart', 'error');
        });
    }

    // Show notification
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Load cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        // You can implement cart count loading here
        // For now, we'll set it to 0
        document.getElementById('cart-count').textContent = '0';
    });
</script>
@endsection