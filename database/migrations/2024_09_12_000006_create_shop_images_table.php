<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shop_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('image_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_images');
    }
};
