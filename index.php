<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Untitled Document</title>
</head>

<body>
<?php 

function get_json_data($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function calculateprice($new,$old) {
	$new = str_replace( ',', '', $new);
	$old = str_replace( ',', '', $old);
	$change = (($new-$old)/$old)*100;
	$change = number_format((float)$change, 2, '.', '');
	if($new >= $old) {
		return '<div class="change-positive">'.$change.'</div>';
	}
	else {
	return '<div class="change-negative">'.$change.'</div>';
	}
}

function bitcoinshotcode($attr) {
	$url = 'https://api.coindesk.com/v1/bpi/currentprice.json';
    $historical = 'https://api.coindesk.com/v1/bpi/historical/close.json';
	
	$bitcoinprice = json_decode(get_json_data($url));
    $currency = $attr['currency'];
	
	if($currency == 'USD') {
 		$price = str_replace( ',', '', $bitcoinprice->bpi->USD->rate);
		$currencysymbol = '$';
	}
	elseif($currency == 'GBP') {
		$price = str_replace( ',', '', $bitcoinprice->bpi->GBP->rate);
		$currencysymbol = '£';
	}
	elseif($currency == 'EUR') {
		$price = str_replace( ',', '', $bitcoinprice->bpi->EUR->rate);
		$currencysymbol = '€';
	}
	else {
		$price = str_replace( ',', '', $bitcoinprice->bpi->USD->rate);
		$currencysymbol = '$';
	}
	
	$historicalPrice = json_decode(get_json_data($historical));
	$today = date("Y-m-d",strtotime("-1 days"));
	$lastday = date("Y-m-d",strtotime("-2 days"));
	
	
	$changeinpirce = calculateprice($historicalPrice->bpi->$today,$historicalPrice->bpi->$lastday);
	
	$currentyear = date('Y');
	$yeartwodate = $currentyear-1 .'-12-31';
	$yearthreedate = $currentyear-2 .'-12-31';
	$yearfourdate = $currentyear-3 .'-12-31';

	$yeartwo = json_decode(get_json_data($historical.'?start='. $yeartwodate .'&end='. $yeartwodate));
	$yearthee = json_decode(get_json_data($historical.'?start='. $yearthreedate .'&end='. $yearthreedate));
	$yearfour = json_decode(get_json_data($historical.'?start='. $yearfourdate .'&end='. $yearfourdate));
	

	$out = '';
	$out .= '<div class="bitcoingraph">';
		$out .= '<h2 class="bitcoin-title">'.$attr['title'].'</h2>';
		$out .= '<div class="bitcoin-change">'.$currency." ".$changeinpirce."%".'</div>';
		$out .= '<div class="price">'.$currencysymbol.$price.'</div>';
		$out .= '<div class="year-data">';
			$out .= '<div class="years">1 Year:'. calculateprice($price,$yeartwo->bpi->$yeartwodate).'</div>';
			$out .= '<div class="years">2 Year:'. calculateprice($yeartwo->bpi->$yeartwodate,$yearthee->bpi->$yearthreedate) .'</div>';
			$out .= '<div class="years">3 Year:'. calculateprice($yearthee->bpi->$yearthreedate,$yearfour->bpi->$yearfourdate) .'</div>';
		$out .= '</div>';
	//here is the price required fpr the graph for a month
		foreach($historicalPrice->bpi as $prices) {
			echo $prices;
		}
	$out .= '</div>';
	
	echo $out;
	
}
$attr = array('currency'=>'USD', 'title'=>'Bitcoin');
echo bitcoinshotcode($attr);
?>
</body>
</html>