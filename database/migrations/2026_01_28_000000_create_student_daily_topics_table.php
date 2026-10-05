<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
    public function up(): void
    {
        if (!Schema::hasTable('student_daily_topics')) {
            Schema::create('student_daily_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_daily_status_id')->constrained()->onDelete('cascade');
            $table->string('topic')->nullable();
            $table->string('starting_time')->nullable();
            $table->string('ending_time')->nullable();
            $table->string('duration')->nullable(); // Can store formatted duration or minutes
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_daily_topics');
    }
};
