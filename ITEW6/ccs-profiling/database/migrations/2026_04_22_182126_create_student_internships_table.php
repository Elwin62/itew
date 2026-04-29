<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('company_address');
            $table->string('supervisor_name');
            $table->string('supervisor_contact');
            $table->string('role');
            $table->string('duration');
            $table->enum('status', ['Ongoing', 'Completed', 'Pending'])->default('Pending');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_internships'); }
};
