<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('category');
            $table->string('sanction');
            $table->enum('status', ['Pending', 'Resolved', 'Under Review'])->default('Pending');
            $table->string('reported_by');
            $table->date('date_reported');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_violations'); }
};
