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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', [
                'draft',
                'quoted',
                'active',
                'completed',
                'cancelled',
            ])->default('draft');
            $table->date('due_date')->nullable();
            $table->index(['organisation_id', 'status']);
            $table->index('client_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
