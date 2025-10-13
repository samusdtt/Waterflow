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
        Schema::create('jar_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('record_date');
            $table->integer('total_refilling')->default(0);
            $table->integer('empty_jars')->default(0);
            $table->integer('onboard_jars')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['supplier_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jar_records');
    }
};