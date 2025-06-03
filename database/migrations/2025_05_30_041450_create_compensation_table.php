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
        Schema::create('compensation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('effective_date');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['allowance', 'bonus', 'commission', 'other'])->default('other');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->nullable();
            $table->foreignId('updated_by')->constrained('users')->nullable();
            $table->softDeletes();
            $table->boolean('is_active')->default(true); // Indicates if the compensation is currently active
            $table->boolean('is_taxable')->default(false); // Indicates if the compensation is subject to tax
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensation');
    }
};
