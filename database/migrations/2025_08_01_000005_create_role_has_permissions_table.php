<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the role_has_permissions pivot table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - permission_id: Foreign key to permissions table
     *   - role_id: Foreign key to roles table
     *
     * Indexes:
     *   - permission_id, role_id for fast lookups
     *
     * Constraints:
     *   - Foreign keys with cascade on update/delete
     *   - Composite primary key for uniqueness
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        // Use direct column names instead of PermissionRegistrar static properties
        $permissionPivotKey = $columnNames['permission_pivot_key'] ?? 'permission_id';
        $rolePivotKey = $columnNames['role_pivot_key'] ?? 'role_id';

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $permissionPivotKey, $rolePivotKey) {
            $table->unsignedBigInteger($permissionPivotKey)->comment('Foreign key to permissions table');
            $table->unsignedBigInteger($rolePivotKey)->comment('Foreign key to roles table');

            // Foreign key constraints
            $table->foreign($permissionPivotKey)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign($rolePivotKey)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Primary key
            $table->primary([
                $permissionPivotKey,
                $rolePivotKey,
            ], 'role_has_permissions_permission_id_role_id_primary');

            // Indexes for performance
            $table->index($permissionPivotKey);
            $table->index($rolePivotKey);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::dropIfExists($tableNames['role_has_permissions']);
    }
};
