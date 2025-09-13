<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->timestamps();

            // 同じユーザーが同じイベントに複数回登録できないようにする
            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_registrations');
    }
};
