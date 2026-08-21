<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Kis employee ki performance hai
            $table->date('date'); // Kis din ki report hai
            $table->integer('tasks_completed')->default(0); // Kitne tasks complete hue
            $table->integer('total_time_spent')->default(0); // Total kitne minutes kaam kiya
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_performances');
    }
};