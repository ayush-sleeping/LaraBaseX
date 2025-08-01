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
        $tableNames  = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams       = config('permission.teams');
        // -----------------------------------------------------------------------------------------------
            Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
                $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission)->comment('Foreign key to permissions table');
                $table->string('model_type')->comment('Model class name');
                $table->unsignedBigInteger($columnNames['model_morph_key'])->comment('Model primary key');
                $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');
                $table->foreign(PermissionRegistrar::$pivotPermission)
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                if ($teams) {
                    $table->unsignedBigInteger($columnNames['team_foreign_key'])->comment('Foreign key to teams table');
                    $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');
                    $table->primary([
                        $columnNames['team_foreign_key'],
                        PermissionRegistrar::$pivotPermission,
                        $columnNames['model_morph_key'],
                        'model_type',
                    ], 'model_has_permissions_permission_model_type_primary');
                } else {
                    $table->primary([
                        PermissionRegistrar::$pivotPermission,
                        $columnNames['model_morph_key'],
                        'model_type',
                    ], 'model_has_permissions_permission_model_type_primary');
                }
                // Additional indexes for performance
                $table->index('model_type');
                $table->index($columnNames['model_morph_key']);
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
