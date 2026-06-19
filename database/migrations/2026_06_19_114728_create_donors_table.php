<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id('donor_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('blood_group', 10);
            $table->string('gender', 20);
            $table->string('phone', 20);
            $table->text('address');
            $table->string('district', 100);
            $table->date('date_of_birth');
            $table->timestamp('last_donated_at')->nullable();
            $table->string('status', 20)->default('available'); // available, unavailable, banned
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals', 'hospital_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};