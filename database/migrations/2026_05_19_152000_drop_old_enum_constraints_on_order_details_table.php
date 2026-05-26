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
        // Drop legacy postgres enum check constraints on order_details table
        DB::statement('ALTER TABLE order_details DROP CONSTRAINT IF EXISTS order_details_status_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
