<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncrementToCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currency', function (Blueprint $table) {
            $table->string('increment')->after('max_amount')->default(1);
        });

        DB::table('currency')->update(
            [
                'increment' => 10000,
            ]
        );

        DB::table('currency')->insert(
            [
                'country' => 'China',
                'currency_name' => 'Chinese Yuan Renminbi',
                'currency_code' => 'CNY',
                'symbol' => '¥',
                'symbol_location' => 'before',
                'default_amount' => 50000,
                'max_amount' => 5000000,
                'increment' => 25000,
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
        //
    }
}
