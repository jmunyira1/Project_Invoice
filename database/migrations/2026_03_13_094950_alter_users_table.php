<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organisation_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('organisations')
                  ->nullOnDelete();

            $table->enum('role', ['owner', 'member'])
                  ->default('member')
                  ->after('password');

            $table->boolean('is_super_admin')
                  ->default(false)
                  ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organisation_id']);
            $table->dropColumn(['organisation_id', 'role', 'is_super_admin']);
        });
    }
};
