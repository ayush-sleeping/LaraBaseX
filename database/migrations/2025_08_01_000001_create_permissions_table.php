<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the permissions table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - id: Primary key
     *   - permissiongroup_id: Foreign key to permissiongroups table
     *   - name: Permission name (unique per guard)
     *   - guard_name: Guard name (e.g., 'web', 'api')
     *   - methods: JSON/text of allowed HTTP methods/actions
     *
     * Indexes:
     *   - permissiongroup_id, name, guard_name for fast lookups
     *
     * Constraints:
     *   - Unique (name, guard_name)
     *   - Foreign key with cascade on update/delete
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        // -----------------------------------------------------------------------------------------------
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Primary key: Permission ID');
            $table->bigInteger('permissiongroup_id')->unsigned()->comment('Foreign key to permissiongroups table');
            $table->string('name', 125)->default('')->comment('Permission name');
            $table->string('guard_name', 125)->default('web')->comment('Guard name for the permission');
            $table->text('methods')->nullable()->comment('HTTP methods or actions allowed');
            $table->timestamps();
            $table->foreign('permissiongroup_id')->references('id')->on('permissiongroups')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['name', 'guard_name']);
            $table->index('permissiongroup_id');
            $table->index('name');
            $table->index('guard_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::dropIfExists($tableNames['permissions']);
    }
};
