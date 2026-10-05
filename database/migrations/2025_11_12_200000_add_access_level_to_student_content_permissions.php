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
        Schema::table('student_content_permissions', function (Blueprint $table) {
            $table->enum('access_level', ['none', 'view', 'download'])
                ->default('download')
                ->after('has_access')
                ->comment('For files: none=no access, view=view only, download=view and download');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_content_permissions', function (Blueprint $table) {
            $table->dropColumn('access_level');
        });
    }
};
