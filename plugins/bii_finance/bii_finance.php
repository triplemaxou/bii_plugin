<?php
/*
  Plugin Name: Bii Finance
  Description: Ajoute des fonctionnalités Financières, fonctionne avec stock-market-widgets
  Version: 0.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_finance_version', '0.1');
define('bii_finance_path', plugin_dir_path(__FILE__));
define('bii_finance_url', plugin_dir_url(__FILE__));
define('bii_finance_now', time());
define('bii_finance_oneyearago', time() - 31556926);

function bii_finance_liste_symbol_Cac40($value = null) {
	return [
		'AC.PA', 'ACA.PA', 'AI.PA', 'AIR.PA', 'BN.PA', 'BNP.PA', 'CA.PA', 'CAP.PA', 'CS.PA', 'DG.PA', 'EI.PA', 'EN.PA',
		'ENGI.PA', 'FP.PA', 'FR.PA', 'GLE.PA', 'KER.PA', 'LHN.PA', 'LI.PA', 'LR.PA', 'MC.PA', 'ML.PA', 'MT.PA', 'NOKIA.PA',
		'OR.PA', 'ORA.PA', 'PUB.PA', 'RI.PA', 'RNO.PA', 'SAF.PA', 'SAN.PA', 'SGO.PA', 'SOLB.PA', 'SU.PA', 'SW.PA', 'TEC.PA',
		'UG.PA', 'UL.PA', 'VIE.PA', 'VIV.PA'
	];
}
function bii_finance_liste_symbol_side($value = null) {
	$array[] = "^FCHI";
	return array_merge($array,  bii_finance_entreprise::get_GroupValues("^FCHI", 3, 3, 0,0));
}
function bii_finance_liste_symbol_DJ($value = null) {
	return [
		'V','IBM','TRV','XOM','DIS','AXP','CVX','CSCO','GE','MCD','MMM','INTC','MRK','JPM','DD','WMT','MSFT','JNJ','UTX','PG',
		'CAT','GS','HD','UNH','KO','PFE','AAPL','NKE','VZ','BA'
	];
}

function bii_finance_put_all_cac40() {
	bii_finance_entreprise::delete_old();
	$results = get_option("bii_finance_last_result");
	$time = time();
	$threehoursago = $time - 3 * 3600;
//	$threehoursago = $time;
	if ($results < $threehoursago) {

		$listecac40 = apply_filters("bii_finance_liste_cac40", "");
		$url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20IN%20(";
		$sep = "";
		foreach ($listecac40 as $symbol) {
			$url.= $sep . '"' . $symbol . '"';
			$sep = "%2C";
		}
		$url.= ")&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
//	pre($url);
		$content = @file_get_contents($url);
		if ($content) {
			update_option("bii_finance_last_result", $time);
			$json = json_decode($content);
			$quotes = $json->query->results->quote;
			if (is_array($quotes)) {
//		pre($quotes, "green");
				foreach ($quotes as $quote) {
					$symbol = $quote->symbol;
					$last_price = $quote->LastTradePriceOnly;
					$change = str_replace("%", "", $quote->PercentChange);
					$nom = $quote->Name;
					$category_symbol = "^FCHI";
					bii_finance_entreprise::add_new($symbol, $last_price, $change, $nom, $category_symbol);
				}
			}
		}

//	;
	}
}
function bii_finance_put_all_DJ() {
	bii_finance_entreprise::delete_old();
	$results = get_option("bii_finance_last_result_dj");
	$time = time();
	$threehoursago = $time - 3 * 3600;
	$threehoursago = $time;
	if ($results < $threehoursago) {

		$liste = apply_filters("bii_finance_liste_symbol_DJ", "");
		$url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20IN%20(";
		$sep = "";
		foreach ($liste as $symbol) {
			$url.= $sep . '"' . $symbol . '"';
			$sep = "%2C";
		}
		$url.= ")&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
//	pre($url);
		$content = @file_get_contents($url);
		if ($content) {
			update_option("bii_finance_last_result_dj", $time);
			$json = json_decode($content);
			$quotes = $json->query->results->quote;
			if (is_array($quotes)) {
//		pre($quotes, "green");
				foreach ($quotes as $quote) {
					$symbol = $quote->symbol;
					$last_price = $quote->LastTradePriceOnly;
					$change = str_replace("%", "", $quote->PercentChange);
					$nom = $quote->Name;
					$category_symbol = "^DJI";
					bii_finance_entreprise::add_new($symbol, $last_price, $change, $nom, $category_symbol);
				}
			}
		}else{
//		pre("nocontent",'red');
	}

//	;
	}else{
//		pre("nodj");
	}
}

function bii_include_class_finance() {
	$liste_class = [
		"bii_finance_item",
		"bii_finance_entreprise"
	];
//	bii_write_log($liste_class);
	foreach ($liste_class as $class) {
		require_once(bii_finance_path . "class/$class.class.php");
		if ($class != "bii_finance_item" && class_exists($class)) {
			bii_custom_log($class);
			if (!$class::table_exists()) {
				$class::autoTable(1);
			}
		}
	}
}

function bii_finance_menu() {
	bii_finance_entreprise::displaySousMenu();
}

add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">
		<tr><th colspan="2">Bii_finance</th>
		<tr><td>Bii_Finance est </td><td><?= bii_makebutton("bii_finance_activated"); ?></td></tr>
	</tbody>
	<?php
}, 12);

function bii_currencytoflag($cur) {
	switch ($cur) {
		case "XAU":
		case "XAUUSD=X":
			$flag = "gold";
			break;
		default:
			$flag = substr(strtolower($cur), 0, 2);
			break;
	}
	return $flag;
}

function bii_trad_currency($symbol) {
	//EURUSD=X
//	$symbol1 = substr($string, 0, 3);
	$symbol2 = substr($symbol, 3, 3);
	?>
	<span><i class="<?= bii_currencytoflag($symbol) ?> sm-flag"></i><i class="<?= bii_currencytoflag($symbol2) ?> sm-flag"></i></span>
	<?php
}

function bii_finance_SC_graph($args = []) {
	ob_start();
	$symbol = "";
	if (isset($args["symbol"])) {
		$symbol = $args["symbol"];
	}
	$days = 30;
	if (isset($args["days"])) {
		$days = $args["days"];
	}
	$start_date = date("Y-m-d", bii_finance_oneyearago);
	if (isset($args["start_date"])) {
		$start_date = $args["start_date"];
	}
	$end_date = date("Y-m-d", bii_finance_now);
	if (isset($args["end_date"])) {
		$end_date = $args["end_date"];
	}
	$field = __("Close");
	if (isset($args["field"])) {
		$field = $args["field"];
	}
	$width = "";
	if (isset($args["width"])) {
		$width = $args["width"];
	}
	$height = 500;
	if (isset($args["height"])) {
		$height = $args["height"];
	}
	$colorgraph = "#7DBEE7";
	if (isset($args["colorgraph"])) {
		$colorgraph = $args["colorgraph"];
	}
	if ($symbol) {
		?>
		<div id="chart-<?= $symbol ?>-LineChart"
			 class="dschart"
			 data-symbol="<?= $symbol ?>"
			 data-type="LineChart"
			 data-start-date="<?= $start_date ?>"
			 data-end-date="<?= $end_date ?>"
			 data-chart-options='{"legend":"none","title":"","titleTextStyle":{"color":"#940101","fontSize":12},"tooltip":{"isHtml":true},"areaOpacity":0.3,"aggregationTarget":"auto","backgroundColor":"#ffffff","colors":["#7dbee7"],"candlestick":{"fallingColor":{"stroke":"#a10000","strokeWidth":1,"fill":"#ff0000"},"risingColor":{"stroke":"#125c06","strokeWidth":1,"fill":"#13d420"}},"chartArea":{"width":"80%","backgroundColor":{"fill":"#ffffff"}},"fontSize":12,"hAxis":{"title":"","titleTextStyle":{"color":"#082278","fontSize":12},"textStyle":{"color":"#082278"},"gridlines":{"color":"#d1d1d1"}},"vAxis":{"title":"","titleTextStyle":{"color":"#082278","fontSize":12},"textStyle":{"color":"#082278"},"format":"decimal","gridlines":{"color":"#d1d1d1"}},"explorer":{"axis":"horizontal","zoomDelta":1.2}}' 
			 data-field="<?= $field ?>"
			 data-zoom="true"
			 data-navigation="true"
			 data-navigation-days="<?= $days ?>"
			 data-navigation-options='{"filterColumnIndex":0,"ui":{"chartType":"AreaChart","chartOptions":{"chartArea":{"width":"80%","height":"100%"},"hAxis":{"baselineColor":"none"},"vAxis":{"baselineColor":"none"}},"chartView":{"columns":[0,1]},"minRangeSize":86400000}}' 
			 data-width="" 
			 data-height="<?= $height ?>"
			 data-indicators='[]'>
		</div>
		<?php
	}
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_finance_SC_CAC40($args = []) {
//	$symbols = [
//		"TEC.PA",
//		"EI.PA",
//		"PUB.PA",
//		"SAN.PA",
////		"MT.PA",
//		"UG.PA",
//		"CA.PA",
//		"LHN.PA",
//		"SGOB.PA",
//		"NUM.PA",
//		"BNP.PA",
//		"AIR.PA",
////		"LOR.PA",
////		"RNL.PA",
//		"RNL.PA",
//		"GLE.PA",
//		"NOKIA.PA",
//		"ORA.PA",
//		"CS.PA",
//		"BN.PA",
//	];
	$symbols = bii_finance_entreprise::get_Cac40Ivalues();
	foreach ($symbols as $symbol) {
		echo do_shortcode("[bii_finance_item_marquee symbol=$symbol]");
	}
}

function bii_finance_SC_DJ($args = []) {
//	$symbols = [
//		"TEC.PA",
//		"EI.PA",
//		"PUB.PA",
//		"SAN.PA",
////		"MT.PA",
//		"UG.PA",
//		"CA.PA",
//		"LHN.PA",
//		"SGOB.PA",
//	];
	$symbols = bii_finance_entreprise::get_DJIvalues();
	foreach ($symbols as $symbol) {
		echo do_shortcode("[bii_finance_item_marquee symbol=$symbol]");
	}
}

function bii_finance_SC_marquee($args = []) {
	ob_start();
	$symbols = [
		"^FCHI", //cac 40
		"^DJI", //Dow jones
		"^GSPC",
		"^IXIC", //Nasdaq
		"^FTSE", //Nasdaq
		"^N225", //Nikkei
		"^GDAXI", //Nikkei
	];
	if (isset($args["symbols"])) {
		$symbols_str = $args["symbols"];
		$symbols = explode(',', $symbols_str);
	}
	if ($symbols) {
		?>
		<div class="sm-marquee">
			<?php
			foreach ($symbols as $symbol) {
				echo do_shortcode("[bii_finance_item_marquee symbol=$symbol]");
			}
			echo bii_finance_SC_CAC40();
			echo bii_finance_SC_DJ();
			?>
		</div>
		<?php
	}
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_finance_SC_currencymarquee($args = []) {
	ob_start();
	$symbols = ['EURUSD=X', 'GBPUSD=X', 'CHFUSD=X', 'BRLUSD=X', 'RUBUSD=X', 'CNYUSD=X', 'JPYUSD=X', 'CADUSD=X', 'XAUUSD=X'];
	if (isset($args["symbols"])) {
		$symbols_str = $args["symbols"];
		$symbols = explode(',', $symbols_str);
	}

	if ($symbols) {
		?>
		<div class="sm-marquee">
			<?php
			foreach ($symbols as $symbol) {
				echo do_shortcode("[bii_finance_item_marquee symbol='$symbol' currency='1']");
			}
			?>
		</div>
		<?php
	}
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_finance_SC_item_marquee($args = []) {
	ob_start();
	$symbol = "";
	if (isset($args["symbol"])) {
		$symbol = $args["symbol"];
	}
	$currency = false;
	if (isset($args["currency"])) {
		$currency = $args["currency"];
	}
	if ($symbol) {
		?>
		<span class="sm-widget sm-widget-ticker" data-type="quote" data-symbol="<?= $symbol ?>">
			<span class="sm-data-property sm-company" data-property="Name"></span>
			<?php
			if ($currency) {
				echo bii_trad_currency($symbol);
			}
			?>
			<i class="caret sm-icon"></i>
			<span class="sm-data-property sm-quote" data-property="LastTradePriceOnly"></span>
			(<span class="sm-data-property sm-pct" data-property="PercentChange"></span>)
		</span>
		<?php
	}
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_finance_SC_animatedtile($args = []) {
	ob_start();
	$symbol = "";
	if (isset($args["symbol"])) {
		$symbol = $args["symbol"];
	}
	if ($symbol) {
		?>
		<div class="sm-widget sm-card" data-type="quote" data-symbol="<?= $symbol ?>" data-loader="true">
			<div class="sm-symbol"><span class="sm-data-property" data-property="Symbol"></span></div>
			<div class="sm-company"><span class="sm-data-property" data-property="Name"></span></div>
			<div class="sm-quote-div">
				<span class="sm-data-property sm-quote odometer" data-property="LastTradePriceOnly"></span>
				<span class="sm-data-property sm-change" data-property="PercentChange"></span>                  
			</div>
		</div>
		<?php
	}
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_finance_list_shortcodes() {
	?>
	<tr>
		<td><strong>[bii_finance_tile symbol='XXXXX (ex GOOGL)']</strong></td>
		<td>Affiche une tile d'informations sur XXXXX
			<?= do_shortcode("[bii_finance_tile symbol='GOOGL']"); ?>
		</td>
	</tr>
	<tr>
		<td><strong>[bii_finance_marquee symbols='XXXXX,XXXXX']</strong></td>
		<td>Affiche un bloc défilant</td>
	</tr>
	<tr>
		<td><strong>[bii_finance_currency_marquee symbols='XXXXX,XXXXX']</strong></td>
		<td>Affiche un bloc défilant du taux de change monétaire</td>
	</tr>
	<tr>
		<td><strong>[bii_finance_graph symbol='XXXXX']</strong></td>
		<td>Affiche un graphique</td>
	</tr>

	<?php
}

function bii_finance_tile_side(){
	$list = apply_filters("bii_finance_liste_symbol_side","");
	ob_start();
	foreach($list as $symbol){
		echo do_shortcode("[bii_finance_tile symbol='$symbol']");
	}



	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_finance_btw_content_clearfix() {
	do_action('bii_finance_put_all_cac40');
	do_action('bii_finance_put_all_DJ');
	?><div class='bii_finance_marquee_container'>
		<div class='bii_marquees'><?php
			echo do_shortcode("[bii_finance_currency_marquee]");
			echo do_shortcode("[bii_finance_marquee]");
			?>
		</div>
		<div class='bii_finance_marquee_dialog'><?= do_shortcode("[bii_finance_tile symbol='GOOGL']"); ?></div>
	</div>
	<?php
}

function bii_finance_enqueueJS() {
	wp_enqueue_script('bii_finance', bii_finance_url . "js/bii_finance.js", array('jquery', 'util', "jquery-ui-dialog"), false, true);
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style("wp-jquery-ui-dialog");
}

if (get_option("bii_finance_activated") && get_option("bii_useclasses")) {
	add_shortcode("bii_finance_tile", "bii_finance_SC_animatedtile");
	add_shortcode("bii_finance_item_marquee", "bii_finance_SC_item_marquee");
	add_shortcode("bii_finance_marquee", "bii_finance_SC_marquee");
	add_shortcode("bii_finance_currency_marquee", "bii_finance_SC_currencymarquee");
	add_shortcode("bii_finance_graph", "bii_finance_SC_graph");
	add_shortcode("bii_finance_CAC40", "bii_finance_SC_CAC40");
	add_shortcode("bii_finance_tile_side", "bii_finance_tile_side");

	add_action("bii_specific_shortcodes", "bii_finance_list_shortcodes");
	add_action("bii_finance_btw_content_clearfix", "bii_finance_btw_content_clearfix");

	add_action('wp_enqueue_scripts', "bii_finance_enqueueJS");

	add_filter("bii_finance_liste_cac40", "bii_finance_liste_symbol_Cac40");
	add_filter("bii_finance_liste_symbol_DJ", "bii_finance_liste_symbol_DJ");
	add_filter("bii_finance_liste_symbol_side", "bii_finance_liste_symbol_side");
	add_action("bii_after_include_class", "bii_include_class_finance", 10);
	add_action("bii_add_menu_pages", "bii_finance_menu");
	add_action("bii_finance_put_all_cac40", "bii_finance_put_all_cac40");
	add_action("bii_finance_put_all_DJ", "bii_finance_put_all_DJ");
}
