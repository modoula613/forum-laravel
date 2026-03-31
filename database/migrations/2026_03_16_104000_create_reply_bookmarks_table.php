<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reply_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reply_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'reply_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reply_bookmarks');
    }
};
