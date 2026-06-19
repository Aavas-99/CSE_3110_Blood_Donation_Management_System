<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id('req_id');
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->string('blood_group', 10);
            $table->integer('quantity_units');
            $table->text('message')->nullable();
            $table->string('urgency_level', 20)->default('normal'); // normal, urgent, critical
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, completed, cancelled
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};