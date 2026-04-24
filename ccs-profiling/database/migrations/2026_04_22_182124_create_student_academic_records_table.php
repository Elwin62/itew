<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('academic_year');
            $table->string('semester');
            $table->decimal('gpa', 4, 2);
            $table->string('standing')->default('Good');
            $table->integer('units_enrolled');
            $table->integer('units_passed');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_academic_records'); }
};
