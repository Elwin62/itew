<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teacher_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('faculty_id');
            $table->string('faculty_name');
            $table->string('subject_code');
            $table->enum('type', ['Commendation', 'Warning', 'General'])->default('General');
            $table->text('comment');
            $table->timestamps();
            $table->index('student_id');
        });
    }
    public function down(): void { Schema::dropIfExists('teacher_feedbacks'); }
};
