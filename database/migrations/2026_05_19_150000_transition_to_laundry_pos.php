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
        // 1. Create customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->index();
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->enum('member_status', ['regular', 'VIP'])->default('regular');
            $table->integer('loyalty_points')->default(0);
            $table->timestamps();
        });

        // 2. Create services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->enum('unit', ['kg', 'pcs', 'meter', 'pasang'])->default('kg');
            $table->decimal('price_per_unit', 15, 2);
            $table->integer('estimated_duration_hours')->default(48);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Modify orders table for laundry
        Schema::table('orders', function (Blueprint $table) {
            // Add laundry-specific columns
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // Since users table uses UUID as PK:
            $table->uuid('staff_production_id')->nullable();
            $table->foreign('staff_production_id')->references('id')->on('users')->nullOnDelete();
            
            // We use string for order_status to easily accommodate new custom laundry statuses
            $table->string('order_status')->default('diterima')->change();
            
            $table->decimal('total_weight_qty', 8, 2)->default(0);
            $table->decimal('delivery_fee', 15, 2)->default(0);
            
            // Payment columns
            $table->string('payment_status')->default('pending')->change(); // pending, dp, paid, refunded
            $table->decimal('dp_amount', 15, 2)->nullable();
            
            // Notes & Timestamps
            $table->text('special_instructions')->nullable();
            $table->timestamp('estimated_completed_at')->nullable();
            $table->timestamp('actual_completed_at')->nullable();
            $table->timestamp('picked_up_delivered_at')->nullable();
        });

        // 4. Modify order_details table
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->decimal('qty', 8, 2)->change(); // Support decimal weight e.g. 2.45 kg
            $table->text('item_condition')->nullable(); // Noda, sobek, dll
            $table->string('photo_path')->nullable(); // Foto kondisi pakaian
        });

        // 5. Create order_status_logs table
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status')->index();
            $table->uuid('changed_by');
            $table->foreign('changed_by')->references('id')->on('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Create pickups_deliveries table
        Schema::create('pickups_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Courier is a user, which has UUID
            $table->uuid('courier_id')->nullable();
            $table->foreign('courier_id')->references('id')->on('users')->nullOnDelete();
            
            $table->enum('type', ['pickup', 'delivery'])->index();
            $table->enum('status', ['assigned', 'on_way', 'completed', 'failed'])->default('assigned')->index();
            $table->text('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('proof_photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickups_deliveries');
        Schema::dropIfExists('order_status_logs');
        
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn(['service_id', 'item_condition', 'photo_path']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['staff_production_id']);
            $table->dropColumn([
                'customer_id', 'staff_production_id', 'total_weight_qty', 'delivery_fee', 
                'dp_amount', 'special_instructions', 'estimated_completed_at', 
                'actual_completed_at', 'picked_up_delivered_at'
            ]);
        });

        Schema::dropIfExists('services');
        Schema::dropIfExists('customers');
    }
};
