<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('subject_name');
            $table->string('room');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('faculty_id');
            $table->string('section');
            $table->enum('type', ['Regular', 'Laboratory', 'Event'])->default('Regular');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('schedules'); }
};
