<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reply_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reply_id')->constrained()->cascadeOnDelete();
            $table->text('old_content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reply_edits');
    }
};
