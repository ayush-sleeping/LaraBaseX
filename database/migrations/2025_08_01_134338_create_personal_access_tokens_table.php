<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the personal_access_tokens table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - id: Primary key
     *   - tokenable_type: Model class name (e.g., App\\Models\\User)
     *   - tokenable_id: Model primary key
     *   - name: Token name for identification
     *   - token: Hashed token value (unique)
     *   - abilities: JSON array of token abilities/permissions
     *   - last_used_at: Last time token was used
     *   - expires_at: Token expiration timestamp
     *   - timestamps: Created/updated at
     *
     * Indexes:
     *   - tokenable_type, tokenable_id, token, expires_at for fast lookups
     *
     * Constraints:
     *   - Unique token for security
     */
    public function up(): void
    {
        // -----------------------------------------------------------------------------------------------
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id()->comment('Primary key: Token ID');
            $table->morphs('tokenable');
            $table->text('name')->comment('Token name for identification');
            $table->string('token', 64)->unique()->comment('Hashed token value');
            $table->text('abilities')->nullable()->comment('JSON array of token abilities/permissions');
            $table->timestamp('last_used_at')->nullable()->comment('Last time token was used');
            $table->timestamp('expires_at')->nullable()->index()->comment('Token expiration timestamp');
            $table->timestamps();

            // Additional indexes for performance
            $table->index('tokenable_type');
            $table->index('tokenable_id');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
