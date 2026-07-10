<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add deposit/installment awareness to payments:
     *  - kind:          what sort of payment this is
     *  - installment_id: the scheduled installment it settles (optional)
     * (document_id already exists and holds the invoice being paid.)
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('kind', ['deposit', 'part_payment', 'balance', 'installment', 'refund'])
                ->default('part_payment')
                ->after('document_id');
            $table->foreignId('installment_id')
                ->nullable()
                ->after('kind')
                ->constrained('installments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('installment_id');
            $table->dropColumn('kind');
        });
    }
};
