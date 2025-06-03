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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['sick', 'vacation', 'maternity', 'paternity', 'bereavement', 'unpaid'])->default('vacation');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('reason')->nullable(); // Reason for the leave
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // User who approved the leave
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // User who created the leave request
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // User who last updated the leave request
            $table->json('metadata')->nullable(); // Additional metadata for the leave request
            $table->softDeletes(); // Soft delete for the leave request
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
