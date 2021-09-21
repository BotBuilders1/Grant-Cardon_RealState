<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('schedule_code');

            $table->string('email');
            $table->string('name');
            $table->string('phone');
            $table->string('timezone');
            $table->dateTime('schedule')->nullable();
            $table->dateTime('standard_datetime')->nullable();
            $table->boolean('sent')->default(0);

            $table->string('gmt');

            $table->string('live_room_url')->nullable();
            $table->string('replay_room_url')->nullable();
            $table->string('thank_you_url')->nullable();
            
            $table->dateTime('date')->nullable();
            $table->longText('common_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_info');
    }
}
