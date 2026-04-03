<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requested_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['requester_id', 'requested_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_requests');
    }
};
