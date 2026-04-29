<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('subject_code');
            $table->string('section');
            $table->date('date');
            $table->enum('status', ['Present', 'Absent', 'Late'])->default('Present');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'date']);
            $table->index(['subject_code', 'section']);
        });
    }
    public function down(): void { Schema::dropIfExists('attendances'); }
};
