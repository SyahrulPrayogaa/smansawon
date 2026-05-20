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
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unsignedInteger('tab_leave_count')->default(0)->after('status');
            $table->boolean('is_locked')->default(false)->after('tab_leave_count');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->string('lock_reason')->nullable()->after('locked_at');
            $table->timestamp('unlocked_at')->nullable()->after('lock_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'tab_leave_count',
                'is_locked',
                'locked_at',
                'lock_reason',
                'unlocked_at',
            ]);
        });
    }
};
