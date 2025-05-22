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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('date_of_birth');
            $table->date('hire_date');
            $table->decimal('base_salary', 10, 2);
            $table->unsignedBigInteger('department_id')->nullable(); // Make nullable first
            $table->unsignedBigInteger('position_id')->nullable(); // Make nullable first
            $table->string('biometric_id')->nullable()->unique(); // For ZKTeco integration
            $table->timestamps();
            $table->softDeletes(); // For employee offboarding
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
