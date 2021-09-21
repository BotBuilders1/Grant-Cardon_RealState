<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('country');
            $table->string('currency_name');
            $table->string('currency_code');
            $table->string('symbol');
            $table->string('symbol_location');
            $table->integer('default_amount');
            $table->integer('max_amount');
            $table->timestamps();
        });

        DB::table('currency')->insert(
            [
                'country' => 'United States',
                'currency_name' => 'U.S. Dollar',
                'currency_code' => 'USD',
                'symbol' => '$',
                'symbol_location' => 'before',
                'default_amount' => 100000,
                'max_amount' => 1000000,
            ]
        );
        
        DB::table('currency')->insert(
            [
                'country' => '19 states of the EU',
                'currency_name' => 'European Euro',
                'currency_code' => 'EUR',
                'symbol' => 'P',
                'symbol_location' => 'before',
                'default_amount' => 100000,
                'max_amount' => 1000000,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency');
    }
}
