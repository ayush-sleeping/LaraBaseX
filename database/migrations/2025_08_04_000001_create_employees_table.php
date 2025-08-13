<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the employees table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - id: Primary key
     *   - user_id: Foreign key to users table (nullable for future flexibility)
     *   - emp_id: Employee identification number (unique)
     *   - personal_email: Personal email address (separate from work email)
     *   - designation: Job title/position
     *   - created_by, updated_by: User IDs for auditing
     *
     * Indexes:
     *   - user_id, emp_id, designation, created_by, updated_by for fast lookups
     *
     * Constraints:
     *   - Foreign keys with cascade on update/delete
     *   - Unique emp_id for employee identification
     */
    public function up(): void
    {
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id()->comment('Primary key: Employee ID');
                $table->unsignedBigInteger('user_id')->nullable()->comment('Foreign key to users table');
                $table->string('emp_id', 50)->unique()->comment('Employee identification number');
                $table->string('personal_email', 255)->nullable()->comment('Personal email address');
                $table->string('designation', 125)->comment('Job title/position');
                $table->unsignedBigInteger('created_by')->nullable()->comment('User who created the employee record');
                $table->unsignedBigInteger('updated_by')->nullable()->comment('User who last updated the employee record');
                $table->timestamps();

                // Foreign key constraints with cascade
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

                // Indexes for performance
                $table->index('user_id');
                $table->index('emp_id');
                $table->index('designation');
                $table->index('created_by');
                $table->index('updated_by');
                $table->index('personal_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
