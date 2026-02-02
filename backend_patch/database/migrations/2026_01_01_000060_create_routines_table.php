<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->unsignedTinyInteger('weekday'); // 1=Mon ... 7=Sun
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('room')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
};
