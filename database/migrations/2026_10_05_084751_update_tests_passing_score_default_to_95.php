<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tests')->where('passing_score', '!=', 95)->update(['passing_score' => 95]);

        try {
            Schema::table('tests', function (Blueprint $table) {
                $table->integer('passing_score')->default(95)->change();
            });
        } catch (\Throwable $e) {
            // SQLite or older driver may not support column change without doctrine/dbal
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('tests', function (Blueprint $table) {
                $table->integer('passing_score')->default(70)->change();
            });
        } catch (\Throwable $e) {
            //
        }
    }
};
