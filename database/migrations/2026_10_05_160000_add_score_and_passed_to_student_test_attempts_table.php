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
        Schema::table('student_test_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('student_test_attempts', 'score')) {
                $table->decimal('score', 5, 2)->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('student_test_attempts', 'passed')) {
                $table->boolean('passed')->default(false)->after('score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_test_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('student_test_attempts', 'passed')) {
                $table->dropColumn('passed');
            }
            if (Schema::hasColumn('student_test_attempts', 'score')) {
                $table->dropColumn('score');
            }
        });
    }
};
