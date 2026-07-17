<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Whether this organisation issues KRA eTIMS-validated invoices.
     *
     * Only when this is on may a document be titled "Tax Invoice" — without
     * eTIMS control-unit data (CU invoice no. / CU serial / QR) a self-printed
     * "Tax Invoice" is not valid for the buyer to claim input VAT in Kenya.
     * Defaults to off so we never overstate compliance.
     */
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->boolean('etims_enabled')->default(false)->after('default_tax_rate');
        });
    }

    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn('etims_enabled');
        });
    }
};
