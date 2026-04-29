<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject_code');
            $table->string('section');
            $table->string('faculty_id');
            $table->enum('type', ['Assignment', 'Activity', 'Quiz', 'Exam'])->default('Assignment');
            $table->date('due_date');
            $table->integer('total_points')->default(100);
            $table->timestamps();
            $table->index(['subject_code', 'section']);
        });
    }
    public function down(): void { Schema::dropIfExists('assignments'); }
};
