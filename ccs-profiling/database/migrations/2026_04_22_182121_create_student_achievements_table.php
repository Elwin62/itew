<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('type');
            $table->enum('level', ['School', 'Regional', 'National', 'International'])->default('School');
            $table->date('date_received');
            $table->string('proof_url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_achievements'); }
};
