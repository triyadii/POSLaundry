<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop legacy postgres enum check constraints
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_status_check');
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_order_status_check');
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_method_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
