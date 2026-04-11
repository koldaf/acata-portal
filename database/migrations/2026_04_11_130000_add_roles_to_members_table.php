<?php

use App\Models\Members;
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
        Schema::table('members', function (Blueprint $table) {
            $table->enum('role', [
                Members::ROLE_MEMBER,
                Members::ROLE_ADMIN,
                Members::ROLE_SUPER_ADMIN,
            ])->default(Members::ROLE_MEMBER)->after('status');

            $table->foreignId('role_assigned_by')
                ->nullable()
                ->after('role')
                ->constrained('members')
                ->nullOnDelete();

            $table->timestamp('role_assigned_at')->nullable()->after('role_assigned_by');
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropConstrainedForeignId('role_assigned_by');
            $table->dropColumn('role_assigned_at');
            $table->dropColumn('role');
        });
    }
};
