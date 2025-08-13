<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the roles table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - id: Primary key
     *   - team_foreign_key: Foreign key to teams table (if teams enabled)
     *   - name: Role name (unique per guard)
     *   - guard_name: Guard name (e.g., 'web', 'api')
     *   - created_by, updated_by: User IDs for auditing
     *
     * Indexes:
     *   - team_foreign_key, name, guard_name, created_by, updated_by for fast lookups
     *
     * Constraints:
     *   - Unique (name, guard_name) or (team_foreign_key, name, guard_name)
     *   - Foreign keys with cascade on update/delete
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');
        // -----------------------------------------------------------------------------------------------
        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id')->comment('Primary key: Role ID');
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->comment('Foreign key to teams table');
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name', 125)->default('')->comment('Role name');
            $table->string('guard_name', 125)->default('web')->comment('Guard name for the role');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created the role');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who last updated the role');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
            $table->index('name');
            $table->index('guard_name');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::dropIfExists($tableNames['roles']);
    }
};
