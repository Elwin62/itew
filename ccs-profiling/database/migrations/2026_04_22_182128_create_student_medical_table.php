<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_medical', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('blood_type')->nullable();
            $table->json('allergies')->nullable();
            $table->json('conditions')->nullable();
            $table->string('emergency_contact');
            $table->string('emergency_number');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_medical'); }
};
