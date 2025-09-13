<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // プロフィール関連のカラムを追加
            $table->string('profile_image')->nullable()->after('password');
            $table->text('bio')->nullable()->after('profile_image');

            // 論理削除用のカラムを追加
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'bio', 'deleted_at']);
        });
    }
};
