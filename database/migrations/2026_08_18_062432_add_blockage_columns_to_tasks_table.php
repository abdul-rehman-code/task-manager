<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false); // Task blocked hai ya nahi
            $table->text('blockage_reason')->nullable();    // Blockage ki waja/description
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'blockage_reason']);
        });
    }
};