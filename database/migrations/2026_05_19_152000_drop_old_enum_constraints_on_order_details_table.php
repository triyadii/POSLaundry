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
    	if (DB::getDriverName() === 'pgsql') {
        	DB::statement('ALTER TABLE order_details DROP CONSTRAINT IF EXISTS order_details_status_check');
    	}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
