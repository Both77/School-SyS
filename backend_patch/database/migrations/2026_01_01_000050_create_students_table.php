<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_no')->unique();
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('guardian_phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
