<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduledData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sms_id')->nullable();
            $table->string('first_name');
            $table->string('email');
            $table->string('timezone');
            $table->string('live_room_url');
            $table->string('replay_room_url');
            $table->string('thank_you_url');
            
            $table->dateTime('date');
            $table->longText('common_data');
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
        Schema::dropIfExists('scheduled_data');
    }
}
