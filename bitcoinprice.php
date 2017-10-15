<?php
/*
Plugin Name: BitCoin Shortcode
Plugin URI: http://corlax.com
Description: Bitcoin widget with a graph.
Version: 1.0
Author: corlax
Author URI: http://www.corlax.com
License: GPL2
*/

###################################################
####### plugin Code ###############
###################################################


function scroll_script_head()
{

       wp_enqueue_style( 'bitcoin_css', plugins_url( 'css/bitcoin.css', __FILE__ ));
       wp_enqueue_script( 'chartjs', plugins_url( 'libs/chart/chart.min.js', __FILE__ ), array('jquery'), '1.0.0', true );
	   wp_enqueue_script( 'bitcoinjs', plugins_url( 'libs/chart/main.js', __FILE__ ), array('jquery'), '1.0.0', true );
}

add_action('wp_enqueue_scripts', 'scroll_script_head', 99);

add_shortcode('bitcoinprice','bitcoinshotcode');

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
					return '<span class="change-positive">'.$change.'% &#9650;</span>';
				}
				else {
					return '<span class="change-negative">'.$change.'% &#9660;</span>';
				}
}

function bitcoinshotcode($attr) {
	            $chart_data = [];
			    $chart_data[0] = [];
			    $chart_data[1] = [];
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
				
				$changeinprice = calculateprice($historicalPrice->bpi->$today,$historicalPrice->bpi->$lastday);
				
				$currentyear = date('Y');
				$yeartwodate = $currentyear-1 .'-12-31';
				$yearthreedate = $currentyear-2 .'-12-31';
				$yearfourdate = $currentyear-3 .'-12-31';

				$yeartwo = json_decode(get_json_data($historical.'?start='. $yeartwodate .'&end='. $yeartwodate));
				$yearthree = json_decode(get_json_data($historical.'?start='. $yearthreedate .'&end='. $yearthreedate));
				$yearfour = json_decode(get_json_data($historical.'?start='. $yearfourdate .'&end='. $yearfourdate));
				
				$out = '';
				$out .= '<div class="bitcoingraph">';
					$out .= '<h2 class="bitcoin-title">'.$attr['title'].' (24t)</h2>';
					$out .= '<div class="bitcoin-change">'.$currency." ".$changeinprice.'</div>';
					$out .= '<div class="price">'.$currencysymbol.round($price, 2).'</div>';
					$out .= '<div class="year-data">';
						$out .= '<div class="years">1 år: '. calculateprice($price,$yeartwo->bpi->$yeartwodate).'</div>';
						$out .= '<div class="years">2 år: '. calculateprice($yeartwo->bpi->$yeartwodate,$yearthree->bpi->$yearthreedate) .'</div>';
						$out .= '<div class="years">3 år: '. calculateprice($yearthree->bpi->$yearthreedate,$yearfour->bpi->$yearfourdate) .'</div>';
					$out .= '</div>';

					//here is the price required fpr the graph for a month
					foreach ($historicalPrice->bpi as $key => $value) {
						array_push($chart_data[0], $value);
						array_push($chart_data[1], $key);
					}
					$out .= '<div><canvas id="myChart" width="462" height="200"></canvas></div>';	
					$out .= "<script>var chart_data = ".json_encode($chart_data)."</script>";				

				$out .= '</div>';
				return $out;
}