<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Student', 'Faculty'])->default('Student')->after('email');
            $table->string('profile_photo')->nullable()->after('role');
            $table->string('student_id')->nullable()->after('profile_photo');
            $table->string('faculty_id')->nullable()->after('student_id');
            $table->string('contact_number')->nullable()->after('faculty_id');
            $table->string('address')->nullable()->after('contact_number');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('address');
            $table->date('birthdate')->nullable()->after('gender');
            $table->string('civil_status')->nullable()->after('birthdate');
            $table->string('nationality')->nullable()->after('civil_status');
            $table->string('academic_program')->nullable()->after('nationality');
            $table->integer('year_level')->nullable()->after('academic_program');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_photo', 'student_id', 'faculty_id', 'contact_number', 'address', 'gender', 'birthdate', 'civil_status', 'nationality', 'academic_program', 'year_level']);
        });
    }
};
