<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignId('document_id')
                ->nullable()
                ->constrained('documents')
                ->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['mpesa', 'cash', 'bank', 'cheque', 'card']);
            $table->string('reference')->nullable(); // M-Pesa code, cheque number etc.
            $table->date('paid_on');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organisation_id']);
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
