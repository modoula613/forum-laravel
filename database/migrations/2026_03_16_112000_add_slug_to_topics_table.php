<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        $topics = DB::table('topics')->select('id', 'title')->get();

        foreach ($topics as $topic) {
            $baseSlug = Str::slug($topic->title) ?: 'sujet';

            DB::table('topics')
                ->where('id', $topic->id)
                ->update(['slug' => $baseSlug.'-'.$topic->id]);
        }

        Schema::table('topics', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
