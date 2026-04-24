<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('name');
            $table->enum('category', ['Programming', 'Networking', 'Design', 'Soft Skill', 'Sports'])->default('Soft Skill');
            $table->enum('proficiency', ['Beginner', 'Intermediate', 'Advanced'])->default('Beginner');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_skills'); }
};
