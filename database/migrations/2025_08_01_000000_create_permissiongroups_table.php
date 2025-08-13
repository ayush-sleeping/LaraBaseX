<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the permissiongroups table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - id: Primary key
     *   - name: Group name for display (e.g., "User Management")
     *   - controller: Controller name this group relates to (e.g., "UserController")
     *
     * Indexes:
     *   - name, controller for fast lookups
     *
     * Constraints:
     *   - Unique name for group identification
     */
    public function up(): void
    {
        if (! Schema::hasTable('permissiongroups')) {
            Schema::create('permissiongroups', function (Blueprint $table) {
                $table->id()->comment('Primary key: Permission Group ID');
                $table->string('name', 125)->unique()->comment('Group name for display');
                $table->string('controller', 125)->comment('Controller name this group relates to');
                $table->timestamps();

                // Indexes for performance
                $table->index('name');
                $table->index('controller');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissiongroups');
    }
};
