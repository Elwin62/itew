<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subject_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_record_id')->constrained('student_academic_records')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->decimal('grade', 4, 2);
            $table->integer('units');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subject_grades'); }
};
