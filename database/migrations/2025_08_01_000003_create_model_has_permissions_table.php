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
     * Creates the model_has_permissions table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - permission_id: Foreign key to permissions table
     *   - model_type: The model class name (e.g., App\\Models\\User)
     *   - model_morph_key: The model's primary key
     *   - team_foreign_key: Foreign key to teams table (if teams enabled)
     *
     * Indexes:
     *   - model_morph_key, model_type, team_foreign_key for fast lookups
     *
     * Constraints:
     *   - Foreign key with cascade on update/delete
     *   - Composite primary key for uniqueness
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        // Use direct column names instead of PermissionRegistrar static properties
        $permissionPivotKey = $columnNames['permission_pivot_key'] ?? 'permission_id';
        $modelMorphKey = $columnNames['model_morph_key'];
        $teamForeignKey = $columnNames['team_foreign_key'];

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $permissionPivotKey, $modelMorphKey, $teamForeignKey, $teams) {
            $table->unsignedBigInteger($permissionPivotKey)->comment('Foreign key to permissions table');
            $table->string('model_type')->comment('Model class name');
            $table->unsignedBigInteger($modelMorphKey)->comment('Model primary key');

            // Create indexes
            $table->index([$modelMorphKey, 'model_type'], 'model_has_permissions_model_id_model_type_index');

            // Foreign key constraint
            $table->foreign($permissionPivotKey)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade')
                ->onUpdate('cascade');

            if ($teams) {
                $table->unsignedBigInteger($teamForeignKey)->comment('Foreign key to teams table');
                $table->index($teamForeignKey, 'model_has_permissions_team_foreign_key_index');
                $table->primary([
                    $teamForeignKey,
                    $permissionPivotKey,
                    $modelMorphKey,
                    'model_type',
                ], 'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([
                    $permissionPivotKey,
                    $modelMorphKey,
                    'model_type',
                ], 'model_has_permissions_permission_model_type_primary');
            }

            // Additional indexes for performance
            $table->index('model_type');
            $table->index($modelMorphKey);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::dropIfExists($tableNames['model_has_permissions']);
    }
};
