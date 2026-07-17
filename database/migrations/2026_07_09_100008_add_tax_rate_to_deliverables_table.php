<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Per-deliverable tax rate. NULL means "inherit the organisation's default
     * rate"; set to 0 for zero-rated / exempt items.
     */
    public function up(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->nullable()->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }
};
