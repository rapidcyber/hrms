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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->decimal('bonus', 10, 2)->nullable(); // Optional bonus
            $table->decimal('overtime_pay', 10, 2)->nullable(); // Optional overtime pay
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->date('payment_date')->nullable(); // Date when the payroll was paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
