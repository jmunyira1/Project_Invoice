<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * A client's KRA PIN — required on tax invoices for VAT-registered buyers.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('kra_pin')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('kra_pin');
        });
    }
};
