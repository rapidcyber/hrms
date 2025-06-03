<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // who did it
            $table->string('action'); // e.g. "clock_in", "update_employee"
            $table->text('description')->nullable(); // more info
            $table->string('subject_type')->nullable(); // model class
            $table->unsignedBigInteger('subject_id')->nullable(); // model id
            $table->json('properties')->nullable(); // details (before/after)
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
