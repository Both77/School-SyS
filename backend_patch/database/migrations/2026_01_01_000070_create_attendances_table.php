<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->enum('status', ['present','absent','late'])->default('present');
            $table->timestamps();

            $table->unique(['date','student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
