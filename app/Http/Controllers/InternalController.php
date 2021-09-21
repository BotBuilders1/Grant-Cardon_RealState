<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Carbon\Carbon;
Use Redirect;

use App\PromotionInfo;

class InternalController extends Controller
{
	public function percentage($partner, $code)
	{
		if($code !== 'A0@jodl#v920#*hFhs16586f'){
			
			// return Redirect::to('/');

		}

		$data = PromotionInfo::where('partner', $partner);

		$total = $data->count();
		$data = $data->get();
		$with_phone = 0;

		foreach($data as $v){
			if(!empty($v->phone)){
				$with_phone = $with_phone + 1;
			}
		}

		$percent = $with_phone / $total;
		$percent = $percent * 100;

		echo '<h4>Total: ' . $total . '</h4>';
		echo '<h4>With Phone: ' . $with_phone . '</h4>';
		echo '<h4>Percentage: ' . round($percent, 2) . '%</h4>';
	}
}
