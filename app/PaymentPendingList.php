<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentPendingList extends Model
{
    protected $table = 'payment_pending_list';

    protected $fillable = [
		'email',
		'Product',
		'paypal_state',
		'paypal_payment_id',
		'paypal_payer_id',
		'paypal_amount',
	];
}
