<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The tax rate frozen onto a document line at generation time.
     * Defaults to 0 so existing documents keep their original (tax-free) totals.
     */
    public function up(): void
    {
        Schema::table('document_lines', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('document_lines', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }
};
