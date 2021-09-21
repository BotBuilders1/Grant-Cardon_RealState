<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPendingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_pending_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('Product');
            $table->string('paypal_state');
            $table->string('paypal_payment_id');
            $table->string('paypal_payer_id');
            $table->string('paypal_amount');
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
        Schema::dropIfExists('payment_pending_list');
    }
}
