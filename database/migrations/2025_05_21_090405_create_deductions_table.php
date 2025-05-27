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
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->enum('type', ['tax', 'benefits', 'voluntary', 'loan', 'other','custom'])->default('other');
            $table->enum('calculation_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('default_amount', 10, 2);
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->boolean('tax_deductible')->default(false);
            $table->boolean('applies_to_all')->default(false);
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->nullable();
            $table->foreignId('updated_by')->constrained('users')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
