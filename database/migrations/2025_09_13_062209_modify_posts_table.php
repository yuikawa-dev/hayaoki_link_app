<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // posted_atカラムを削除
            $table->dropColumn('posted_at');

            // titleカラムを削除（存在する場合）
            if (Schema::hasColumn('posts', 'title')) {
                $table->dropColumn('title');
            }

            // urlカラムを削除（存在する場合）
            if (Schema::hasColumn('posts', 'url')) {
                $table->dropColumn('url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('posted_at')->nullable();
            $table->string('title')->nullable();
            $table->string('url')->nullable();
        });
    }
};
