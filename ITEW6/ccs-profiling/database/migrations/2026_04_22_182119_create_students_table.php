<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('birthdate');
            $table->string('civil_status')->default('Single');
            $table->string('nationality')->default('Filipino');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->text('address');
            $table->string('profile_photo')->nullable();
            $table->string('academic_program');
            $table->enum('enrollment_status', ['Enrolled', 'Not Enrolled', 'Graduated', 'Dropped'])->default('Enrolled');
            $table->enum('admission_type', ['Regular', 'Transferee', 'Irregular'])->default('Regular');
            $table->string('academic_year')->default('2023-2024');
            $table->string('semester')->default('1st Semester');
            $table->date('date_enrolled');
            $table->integer('year_level');
            $table->string('section');
            $table->boolean('is_scholar')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('students');
    }
};
