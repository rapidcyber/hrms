<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->dateTime('in_1')->nullable();   // First clock-in
            $table->dateTime('out_1')->nullable();  // First clock-out (e.g., break)
            $table->dateTime('in_2')->nullable();   // After-break in
            $table->dateTime('out_2')->nullable();  // After-break out
            $table->dateTime('in_3')->nullable();   // Optional
            $table->dateTime('out_3')->nullable();  // Final clock-out

            $table->decimal('hours_worked', 5, 2)->default(0); // Total hours
            $table->string('status')->nullable(); // e.g., present, absent, late
            $table->text('remarks')->nullable();  // notes
            $table->string('source')->nullable(); // e.g., biometric/manual

            $table->timestamps();

            // $table->unique(['employee_id', 'date']); // One record per day per employee
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
