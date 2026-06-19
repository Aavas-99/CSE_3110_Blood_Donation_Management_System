<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id('donation_id');
            $table->foreignId('donor_id')->constrained('donors', 'donor_id')->onDelete('cascade');
            $table->foreignId('hospital_id')->constrained('hospitals', 'hospital_id')->onDelete('cascade');
            $table->date('date');
            $table->integer('quantity_units');
            $table->string('status', 20)->default('scheduled'); // scheduled, completed, cancelled, no_show
            $table->foreignId('patient_id')->nullable()->constrained('patients', 'patient_id')->onDelete('set null');
            $table->foreignId('req_id')->nullable()->constrained('emergency_requests', 'req_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};