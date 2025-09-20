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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('set null'); // or 'cascade' as needed

            $table->foreign('position_id')
                  ->references('id')
                  ->on('positions')
                  ->onDelete('set null');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('manager_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
        });
    }
};
