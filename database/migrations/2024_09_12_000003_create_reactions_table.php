<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->timestamps();

            // 同じユーザーが同じ投稿に対して同じタイプのリアクションを複数回できないようにする
            $table->unique(['user_id', 'post_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reactions');
    }
};
