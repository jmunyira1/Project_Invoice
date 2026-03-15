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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();
            $table->foreignId('template_id')
                ->constrained('templates');
            $table->enum('type', [
                'quote',
                'invoice',
                'receipt',
                'delivery_note',
                'statement',
            ]);
            $table->string('number')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['organisation_id', 'type']);
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
