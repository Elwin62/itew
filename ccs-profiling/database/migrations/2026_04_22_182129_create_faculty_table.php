<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('faculty', function (Blueprint $table) {
            $table->id();
            $table->string('faculty_id')->unique();
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('birthdate');
            $table->string('civil_status')->default('Single');
            $table->string('nationality')->default('Filipino');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->text('address');
            $table->string('department');
            $table->string('profile_photo')->nullable();
            $table->integer('years_experience')->default(0);
            $table->enum('employment_status', ['Full-time', 'Part-time', 'Contractual'])->default('Full-time');
            $table->string('academic_rank');
            $table->date('date_hired');
            $table->date('contract_end_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('faculty'); }
};
