<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('blood_group', 10);
            $table->string('gender', 20);
            $table->string('phone', 20);
            $table->text('address');
            $table->string('district', 100);
            $table->date('date_of_birth');
            $table->string('status', 20)->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};