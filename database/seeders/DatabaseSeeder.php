<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create super admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@watermanagement.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+1234567890',
            'address' => '123 Admin Street',
            'city' => 'Admin City',
            'state' => 'Admin State',
            'pincode' => '12345',
        ]);

        // Create sample suppliers
        $supplier1 = Supplier::create([
            'name' => 'AquaFresh Water Solutions',
            'email' => 'info@aquafresh.com',
            'phone' => '+1234567891',
            'address' => '456 Water Street, Industrial Area',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'gst_number' => '27ABCDE1234F1Z5',
            'pan_number' => 'ABCDE1234F',
            'subscription_status' => 'active',
            'subscription_start_date' => now()->subMonth(),
            'subscription_end_date' => now()->addMonths(11),
            'monthly_fee' => 5000.00,
            'service_areas' => ['Mumbai', 'Thane', 'Navi Mumbai'],
            'is_active' => true,
        ]);

        $supplier2 = Supplier::create([
            'name' => 'PureDrop Water Services',
            'email' => 'contact@puredrop.com',
            'phone' => '+1234567892',
            'address' => '789 Pure Street, Business District',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'gst_number' => '07FGHIJ5678K1L2',
            'pan_number' => 'FGHIJ5678K',
            'subscription_status' => 'active',
            'subscription_start_date' => now()->subMonth(),
            'subscription_end_date' => now()->addMonths(11),
            'monthly_fee' => 4500.00,
            'service_areas' => ['Delhi', 'Gurgaon', 'Noida'],
            'is_active' => true,
        ]);

        // Create supplier admins
        User::create([
            'name' => 'AquaFresh Admin',
            'email' => 'admin@aquafresh.com',
            'password' => Hash::make('password'),
            'role' => 'supplier_admin',
            'supplier_id' => $supplier1->id,
            'phone' => '+1234567891',
            'address' => '456 Water Street, Industrial Area',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
        ]);

        User::create([
            'name' => 'PureDrop Admin',
            'email' => 'admin@puredrop.com',
            'password' => Hash::make('password'),
            'role' => 'supplier_admin',
            'supplier_id' => $supplier2->id,
            'phone' => '+1234567892',
            'address' => '789 Pure Street, Business District',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
        ]);

        // Create staff members
        User::create([
            'name' => 'Rajesh Kumar',
            'email' => 'rajesh@aquafresh.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'supplier_id' => $supplier1->id,
            'phone' => '+1234567893',
            'address' => '123 Staff Colony',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400002',
        ]);

        User::create([
            'name' => 'Priya Sharma',
            'email' => 'priya@puredrop.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'supplier_id' => $supplier2->id,
            'phone' => '+1234567894',
            'address' => '456 Staff Colony',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110002',
        ]);

        // Create sample clients
        User::create([
            'name' => 'Amit Patel',
            'email' => 'amit@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'supplier_id' => $supplier1->id,
            'phone' => '+1234567895',
            'address' => '789 Client Street, Andheri',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400058',
        ]);

        User::create([
            'name' => 'Sneha Gupta',
            'email' => 'sneha@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'supplier_id' => $supplier2->id,
            'phone' => '+1234567896',
            'address' => '321 Client Avenue, Connaught Place',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
        ]);

        // Create sample products for supplier 1
        Product::create([
            'supplier_id' => $supplier1->id,
            'name' => 'Amust Jar',
            'description' => 'Premium quality 20L water jar',
            'type' => 'jar',
            'size' => '20L',
            'brand' => 'Amust',
            'price' => 50.00,
            'stock_quantity' => 100,
            'min_stock_level' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier1->id,
            'name' => 'Bisleri Jar',
            'description' => 'Trusted 20L water jar',
            'type' => 'jar',
            'size' => '20L',
            'brand' => 'Bisleri',
            'price' => 100.00,
            'stock_quantity' => 80,
            'min_stock_level' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier1->id,
            'name' => 'Amust 200ml Box',
            'description' => 'Convenient 200ml water box',
            'type' => 'box',
            'size' => '200ml',
            'brand' => 'Amust',
            'price' => 105.00,
            'stock_quantity' => 200,
            'min_stock_level' => 20,
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier1->id,
            'name' => 'Bisleri 250ml Box',
            'description' => 'Premium 250ml water box',
            'type' => 'box',
            'size' => '250ml',
            'brand' => 'Bisleri',
            'price' => 135.00,
            'stock_quantity' => 150,
            'min_stock_level' => 20,
            'is_active' => true,
        ]);

        // Create sample products for supplier 2
        Product::create([
            'supplier_id' => $supplier2->id,
            'name' => 'AquaFresh Jar',
            'description' => 'Pure 20L water jar',
            'type' => 'jar',
            'size' => '20L',
            'brand' => 'AquaFresh',
            'price' => 45.00,
            'stock_quantity' => 120,
            'min_stock_level' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier2->id,
            'name' => 'PureDrop Jar',
            'description' => 'Clean 20L water jar',
            'type' => 'jar',
            'size' => '20L',
            'brand' => 'PureDrop',
            'price' => 90.00,
            'stock_quantity' => 90,
            'min_stock_level' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier2->id,
            'name' => 'AquaFresh 200ml Box',
            'description' => 'Portable 200ml water box',
            'type' => 'box',
            'size' => '200ml',
            'brand' => 'AquaFresh',
            'price' => 95.00,
            'stock_quantity' => 180,
            'min_stock_level' => 20,
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier2->id,
            'name' => 'PureDrop 250ml Box',
            'description' => 'Refreshing 250ml water box',
            'type' => 'box',
            'size' => '250ml',
            'brand' => 'PureDrop',
            'price' => 125.00,
            'stock_quantity' => 160,
            'min_stock_level' => 20,
            'is_active' => true,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Super Admin: admin@watermanagement.com / password');
        $this->command->info('Supplier Admin 1: admin@aquafresh.com / password');
        $this->command->info('Supplier Admin 2: admin@puredrop.com / password');
        $this->command->info('Staff 1: rajesh@aquafresh.com / password');
        $this->command->info('Staff 2: priya@puredrop.com / password');
        $this->command->info('Client 1: amit@example.com / password');
        $this->command->info('Client 2: sneha@example.com / password');
    }
}