<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->foreignId('news_article_id')
                ->nullable()
                ->after('category_id')
                ->constrained('news_articles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('news_article_id');
        });
    }
};
