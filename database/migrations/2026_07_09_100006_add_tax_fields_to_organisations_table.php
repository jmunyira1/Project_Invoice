<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * VAT / tax details for the organisation (Kenya: KRA PIN + 16% VAT).
     */
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('kra_pin')->nullable()->after('address');   // KRA PIN / VAT reg no.
            $table->boolean('vat_registered')->default(false)->after('kra_pin');
            $table->decimal('default_tax_rate', 5, 2)->default(16.00)->after('vat_registered');
        });
    }

    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['kra_pin', 'vat_registered', 'default_tax_rate']);
        });
    }
};
