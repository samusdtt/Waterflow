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
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('login_time')->nullable();
            $table->time('logout_time')->nullable();
            $table->integer('total_hours')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['staff_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};