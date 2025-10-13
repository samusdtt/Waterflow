<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['jar', 'box']);
            $table->string('size'); // e.g., '20L', '200ml', '250ml'
            $table->string('brand'); // e.g., 'Amust', 'Bisleri'
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(10);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};