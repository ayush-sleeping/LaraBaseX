<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the enquiries table with all necessary columns, indexes, and constraints.
     * Columns:
     *   - id: Primary key
     *   - first_name: Contact's first name
     *   - last_name: Contact's last name
     *   - email: Contact's email address
     *   - mobile: Contact's mobile number
     *   - message: Inquiry message/content
     *   - remark: Internal remarks/notes from staff
     *   - created_by, updated_by: User IDs for auditing
     *
     * Indexes:
     *   - email, mobile, first_name, last_name, created_by, updated_by for fast lookups
     *
     * Constraints:
     *   - Foreign keys with cascade on update/delete
     */
    public function up(): void
    {
        if (! Schema::hasTable('enquiries')) {
            Schema::create('enquiries', function (Blueprint $table) {
                $table->id()->comment('Primary key: Enquiry ID');
                $table->string('first_name', 125)->comment('Contact\'s first name');
                $table->string('last_name', 125)->comment('Contact\'s last name');
                $table->string('email', 255)->comment('Contact\'s email address');
                $table->string('mobile', 20)->comment('Contact\'s mobile number');
                $table->text('message')->nullable()->comment('Inquiry message/content');
                $table->text('remark')->nullable()->comment('Internal remarks/notes from staff');
                $table->unsignedBigInteger('created_by')->nullable()->comment('User who created the enquiry record');
                $table->unsignedBigInteger('updated_by')->nullable()->comment('User who last updated the enquiry record');
                $table->timestamps();

                // Foreign key constraints with cascade
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

                // Indexes for performance
                $table->index('email');
                $table->index('mobile');
                $table->index('first_name');
                $table->index('last_name');
                $table->index('created_by');
                $table->index('updated_by');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
