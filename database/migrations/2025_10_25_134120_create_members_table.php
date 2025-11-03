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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->enum('email_verified', ['yes', 'no'])->default('no');
            $table->string('password');
            $table->string('membership_type');
            $table->string('member_id')->unique();
            $table->string('phone')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('job_title')->nullable();
            $table->string('country')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('bio')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('social_links')->nullable();
            $table->date('created_on')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
