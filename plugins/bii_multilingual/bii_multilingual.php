<?php
/*
  Plugin Name: bii_multilingual
  Description: Ajoute des fonctions multilingues
  Version: 0.5.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_multilingual_version', '0.5.2');
define('bii_multilingual_path', plugin_dir_path(__FILE__));
define('bii_multilingual_url', plugin_dir_url(__FILE__));


add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">
		<tr><th colspan="2">Bii_Multilingual</th>
		<tr><td>Bii multilingue est </td><td><?= bii_makebutton("bii_use_multilingual"); ?></td></tr>
		<?php
		if (get_option("bii_use_multilingual") && class_exists("GTranslate")) {
			?><tr><td>Les options avec Gtranslate sont </td><td><?= bii_makebutton("bii_use_gtranslate", 1, 1); ?></td></tr><?php
		}
		?>
	</tbody>
	<?php
}, 10);

function bii_multilingual_available_languages() {
	$langs = [
		"fr" => "Français",
		"en" => "Anglais",
		"es" => "Espagnol",
		"pt" => "Portugais",
		"ru" => "Russe",
		"de" => "Allemand",
		"nl" => "Néerlandais",
	];
	return $langs;
}

function bii_multilingual_default_language_selection_admin_script($text) {
	ob_start();
	?>
	<script>
		jQuery(function ($) {
			//			$("#bii_multilingual_languages").hide(0);
			$(".bii_select_languages").on("click", function () {
				var value = $("#bii_multilingual_languages").val();
				var exp = value.split(",");
				var newtab = [];
				var valuetoadd = $(this).attr("data-value");
				if (value.indexOf(valuetoadd) == -1) {

					exp.push(valuetoadd);
					newtab = exp;
					$(this).find(".fa").addClass("fa-check-square-o").removeClass("fa-square-o");
				} else {
					$.each(exp, function (index, v) {
						if (v != valuetoadd) {
							newtab.push(v);
						}
					});
					$(this).find(".fa").removeClass("fa-check-square-o").addClass("fa-square-o");
				}
				bii_CL(newtab);
				var newval = newtab.join();
				$("#bii_multilingual_languages").val(newval);
			});
		});
	</script>
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_multilingual_default_language_admin_selection() {
	$langs = bii_multilingual_available_languages();
	$ret = "<span class='bii_select_languages_wrapper '>";
	foreach ($langs as $lang => $libelle) {
		$selected = "<span class='fa fa-square-o'></span>";
		if (strpos(get_option("bii_multilingual_languages"), $lang) !== false) {
			$selected = "<span class='fa fa-check-square-o'></span>";
		}
		$ret .= "<span class='bii_select_languages ' data-value='$lang'>$selected <img src='" . bii_multilingual_url . "flags/$lang.png' /> $libelle</span>";
	}
	$ret.="</span>";
	$ret .= apply_filters("bii_multilingual_default_language_selection_admin_script", $ret);
	return $ret;
}

function bii_add_multilingual_option_title() {
	?>
	<li role="presentation" class="hide-relative" data-relative="pl-Lang"><i class="fa fa-language"></i> Langues</li>
	<?php
}

function bii_add_multilingual_options() {
	?>
	<div class="col-xxs-12 pl-Lang bii_option hidden">
		<?= bii_makestuffbox("bii_multilingual_languages", "Langues", "text", "col-xxs-12", [], "hidden", apply_filters("bii_multilingual_default_language_admin_selection", null)); ?>
		<?php
		$langs = maybe_unserialize(explode(",", get_option("bii_multilingual_languages")));
//		pre($langs);
		$dl = bii_multilingual_available_languages();
		foreach ($langs as $lang) {
			if (!get_option("bii_multilingual_$lang" . "_name")) {
				update_option("bii_multilingual_$lang" . "_name", $dl[$lang]);
			}
			if (!get_option("bii_multilingual_$lang" . "_flag")) {
				update_option("bii_multilingual_$lang" . "_flag", bii_multilingual_url . "flags/$lang" . ".png");
			}
			bii_makestuffbox("bii_multilingual_$lang" . "_name", "Nom pour $lang", "text", "col-xxs-12 col-xs-6 col-sm-3");
			bii_makestuffbox("bii_multilingual_$lang" . "_flag", "Drapeau pour $lang", "text", "col-xxs-12 col-xs-6 col-sm-3");
		}
		?>
	</div>
	<?php
}

function bii_multilingual_enqueueJS() {
	wp_enqueue_script('bii_multilingual', bii_multilingual_url . "js/multilingual.js", array('jquery', 'util'), false, true);
}

function bii_multilingual_css_front() {
	wp_enqueue_style('bii_multilingual', bii_multilingual_url . '/css/bii_multilingual.css');
}

function bii_multilingual_additionnal_js_var() {
	global $wp_query;
	if (isset($wp_query->query_vars["lang"])) {
		echo "bii_lang = '" . $wp_query->query_vars["lang"] . "';";
	}
	if (ICL_LANGUAGE_CODE) {
		echo "bii_lang = '" . ICL_LANGUAGE_CODE . "';";
	}
	if (isset($_REQUEST["lang"])) {
		echo "bii_lang = '" . $_REQUEST["lang"] . "';";
	}
	echo "bii_multilingual_activated = true;";
//	
//	echo "</script>";
//	
//	consoleLog(get_the_ID());
//	echo "<script>";
}

function bii_multilingual_display_flag($lang) {
	if (!get_option("bii_multilingual_$lang" . "_name")) {
		$dl = bii_multilingual_available_languages();
		update_option("bii_multilingual_$lang" . "_name", $dl[$lang]);
	}
	if (!get_option("bii_multilingual_$lang" . "_flag")) {
		update_option("bii_multilingual_$lang" . "_flag", bii_multilingual_url . "flags/$lang" . ".png");
	}
	$flag = get_option("bii_multilingual_$lang" . "_flag");
	$alt = get_option("bii_multilingual_$lang" . "_name");
	$url = apply_filters("bii_multilingual_real_baseurl", get_bloginfo("url"), $lang);
	$contents = "<span class='bii-select-flag'>"
		. "<a href='$url'>"
		. "<img width='18' height='12' class='lazyquick' alt='$alt' src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' data-original='$flag' />"
		. "</a>"
		. "</span>";
	return $contents;
}

function bii_multilingual_select_flags($val) {
	$langs = maybe_unserialize(explode(",", get_option("bii_multilingual_languages")));
	$output = "<div class='bii-front-select-language'>";
	foreach ($langs as $lang) {
		$output .= apply_filters("bii_multilingual_display_flag", $lang);
	}
	$output .= "</div>";
	return $output;
}

function bii_multilingual_real_baseurl($url, $lang) {
	$url = explode("?", $url);
	$nopoint = $url[0];
//	$id = get_the_ID();
//	$id_translation = icl_translations::get_translation_of($id,$lang);
//	bii_write_log("[id_translation] ". $id_translation);
//	if($id && $id_translation ){
//		return get_post($id_translation)->guid;
//	}
	return $nopoint . "/?lang=$lang";
}

function bii_multilingual_option_submit() {
	$tableaucheck = ["bii_multilingual_languages"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}

	update_option("bii_multilingual_languages_serialized", serialize(explode(",", $_POST["bii_multilingual_languages"])));
	$langs = maybe_unserialize(explode(",", get_option("bii_multilingual_languages")));
	foreach ($langs as $lang) {
		update_option("bii_multilingual_$lang" . "_name", $_POST["bii_multilingual_$lang" . "_name"]);
		update_option("bii_multilingual_$lang" . "_flag", $_POST["bii_multilingual_$lang" . "_flag"]);
	}
}

function bii_multilingual_body_class($classes, $class = "") {
	if (class_exists("icl_translations")) {
		$id = get_the_ID();
		$id_base_trad = icl_translations::get_trad_base_of($id);
		$classes[] = "bii-traduit-de-" . $id_base_trad;
	}
	return $classes;
}

function bii_multilingual_current_language() {
	$lang = 'fr';
	global $wp_query;
	if (isset($wp_query->query_vars["lang"])) {
		$lang = $wp_query->query_vars["lang"];
	}
	if (isset($_REQUEST["langchanged"])) {
		$_REQUEST["lang"] = $_REQUEST["langchanged"];
	}
	if (defined('ICL_LANGUAGE_CODE')) {
		$lang = ICL_LANGUAGE_CODE;
	}
	if (isset($_REQUEST["lang"])) {
		$lang = $_REQUEST["lang"];
	}
	return $lang;
}

function bii_multilingual_add_translation($search_term, $replace_term, &$bii_search, &$bii_replace) {
	$bii_search[] = $search_term;
	$bii_replace[] = $replace_term;
}

function bii_multilingual_adaptative_text($attrs, $content) {
	$lang = bii_multilingual_current_language();
	if (isset($attrs["lang"])) {
		$lang = $attrs["lang"];
	}
	return bii_multilingual_more_translation(__($content));
}

function bii_multilingual_list_shortcodes($attrs, $content) {
	?><tr>
		<td><strong>[bii_autotranslate lang="<?= bii_multilingual_current_language(); ?>"]</strong></td>
		<td>Appelle la fonction de traduction du texte dans la lang choisie, par défaut lang correspond à la langue en cours du site</td>
	</tr><?php
}

function bii_multilingual_include_classes() {
	require_once(bii_multilingual_path . "class/icl_translations.class.php");
	if (strpos(get_permalink(), "/user-information/")) {
		$_REQUEST["lang"] = "en";
	}
}

function bii_multilingual_more_translation($text, $domain = "") {
	$bii_search = [];
	$bii_replace = [];
	$lang = bii_multilingual_current_language();
	$path = bii_multilingual_path . "/translations/$lang.php";
	if(file_exists($path)){
		include($path);
	}
	$rep = str_replace($bii_search, $bii_replace, $text);
	return $rep;
}

function bii_nav_menu_items($items, $args) {
//	logQueryVars();
	$languages = apply_filters('wpml_active_languages', NULL, 'skip_missing=0');
	if ($languages && $args->theme_location == 'primary') {
		if (!empty($languages)) {
			$first = true;

			foreach ($languages as $l) {
				$flagurl = $l['country_flag_url'];
				$flagurl = str_replace("nil.png", $l['code'] . ".png", $flagurl);
				$items = $items . '<li class="menu-item bii_menu-item-language">'
					. '<a href="' . apply_filters("bii_multilingual_link_selector_translation", $l['url'], $l['language_code']) . '">'
					. '<img data-original="' . $flagurl . '" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" height="12" alt="' . $l['language_code'] . '" width="18" class="lazyquick" />'
					. '<span class=""> ' . $l['native_name'] . '</span>'
					. '</a>';
				$items .= '</li>';
				$first = false;
			}
		}
	}

	return $items;
}

function bii_div_flags($class = "") {
	$languages = apply_filters('wpml_active_languages', NULL, 'skip_missing=0');
	$items = "";
	if ($languages && !empty($languages)) {
		$first = true;
		$items = "<div class='bii_switch-language-container $class'>";
		foreach ($languages as $l) {
			$flagurl = $l['country_flag_url'];
			$flagurl = str_replace("nil.png", $l['code'] . ".png", $flagurl);
			$items .= '<div class="bii_switch-language">'
				. '<a href="' . apply_filters("bii_multilingual_link_selector_translation", $l['url'], $l['language_code']) . '">'
				. '<img data-original="' . $flagurl . '" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" height="12" alt="' . $l['language_code'] . '" width="18" class="lazyquick" />'
//				. '<span class=""> ' . $l['native_name'] . '</span>'
				. '</a>'
				. '</div>';

			$first = false;
		}
		$items .= "</div>";
	}
	return $items;
}

function bii_multilingual_widget_titles($title) {
//	$lang = bii_multilingual_current_language();
	$return = __($title);
	return $return;
}

function bii_multilingual_link_traduction($link) {
	$post_id = url_to_postid($link);
	$trad_id = icl_object_id($post_id);
	return get_permalink($trad_id);
}

add_filter('bii_trad_link', "bii_multilingual_link_traduction");

function bii_multilingual_filter_um_localize_permalink($url) {
	$lang = bii_multilingual_current_language();
	$url = str_replace("?lang=$lang", "", $url);

	if ($lang != "fr") {
		$url .= "?langchanged=$lang";
	}
	return $url;
}

function bii_um_multilingual_fix_menu($nav_link) {
	$lang = bii_multilingual_current_language();
	$url = str_replace("?lang=$lang", "", $nav_link);

	if ($lang != "fr") {
		$url .= "&langchanged=$lang";
	}
	return $url;
}

function bii_um_multilingual_tabs($tabs) {
//	pre($tabs);
	$lang = bii_multilingual_current_language();

	if ($lang != "fr") {
		foreach ($tabs as $tab) {
			$tab["name"] = __($tab["name"]);
		}
	}
	return $tabs;
}

function bii_um_multilingual_add_rewrite_rules($aRules) {
	
}

function bii_multilingual_filter_date_i18n($j, $req_format, $i, $gmt) {
	$lang = bii_multilingual_current_language();
	if ($lang == "fr") {
		if ($req_format == "F d, Y") {
			$req_format = "d/m/Y";
		}
	}

	return date($req_format, $i);
}

function bii_um_multilingual_label_title($label) {

	return __($label);
}

function bii_multilingual_link_selector_translation($url, $lang) {
	global $wp_query;
	if ($lang == "fr") {
		$url = str_replace("?langchanged=en", "", $url);
		$url = str_replace("?lang=en", "", $url);
	}

	if ($lang == "en") {
		if (strpos($url, "voir-un-utilisateur") !== false) {
			$permalink = get_permalink();
			$user = "";
			if (isset($wp_query->query_vars["um_user"])) {
				$user = $wp_query->query_vars["um_user"] . "/";
			}
			$url = $permalink . $user . "?langchanged=en";
//			$url = $permalink.
		}
	}
	return $url;
}

function bii_multilingual_menuitem_class() {
	return ' menu-item-object-page ';
}

function bii_multilingual_menuitem_class_wgtranslate() {
	return ' menu-item-object-page menu-item-has-children  dropdown mega-1-cols';
}

function bii_multilingual_submenu_item($something) {
	if (get_option('bii_use_gtranslate')) {
		$filter = '<ul class="dropdown-menu sub-menu pull-right gtranslate-ul" role="menu">' . do_shortcode("[GTranslate]") . '</ul>';
	}
	return $filter;
}

function bii_yith_wcwl_socials_share_title($value) {
	$lang = bii_multilingual_current_language();

	if ($lang == "fr") {
		'<div class="hr-title hr-full"><abbr></abbr>Partager sur les réseaux</div>';
	} else {
		return $value;
	}
}

function bii_multilingual_date_format($value = "") {
	$lang = bii_multilingual_current_language();
	$long = "F j, Y g:i a";
	$shortdays = "m/d/Y";
	$hour = "g:i a";
	if ($lang == "fr") {
		$long = "d/m/Y H:i:s";
		$shortdays = "d/m/Y";
		$hour = "H:i:s";
	}

	return [
		"long" => $long,
		"shortdays" => $shortdays,
		"hour" => $hour,
	];
}

function bii_multilingual_nfum_dataTableOptions($value = "") {
	$lang = bii_multilingual_current_language();
	if ($lang == "fr") {
		$value = ' {"language": { "url": "//cdn.datatables.net/plug-ins/1.10.12/i18n/French.json" }}';
	}
	return $value;
}

function bii_multilingual_date($time, $post_id) {
	$post = get_post($post_id);
//	pre($post);
	$date = $post->post_date;
	$dt = new DateTime($date);
	$now = time();
	$timeposted = $dt->getTimestamp();
	$twominutesago = $now - (2 * 60);
	if ($timeposted > $twominutesago) {
		$lang = bii_multilingual_current_language();
		switch ($lang) {
			case "fr":
				$time = "A l'instant";
				break;
			default:
				$time = "Just now";
				break;
		}
	} else {
		$time = "";
		$lang = bii_multilingual_current_language();
		$today_midnight = timestamp_today_midnight();
		$yesterday_midnight = timestamp_yesterday_midnight();
		if ($timeposted > $today_midnight) {
			switch ($lang) {
				case "fr":
					$time = "Aujourd'hui à ";
					break;
				default:
					$time = "Today at ";
					break;
			}
			$format = bii_multilingual_date_format()["hour"];
			$time .= $dt->format($format);
		}
		elseif ($timeposted > $yesterday_midnight) {
			switch ($lang) {
				case "fr":
					$time = "Hier à ";
					break;
				default:
					$time = "Yesterday at ";
					break;
			}
			$format = bii_multilingual_date_format()["hour"];
			$time .= $dt->format($format);
		}else{
			switch ($lang) {
				case "fr":
					$time = "Le ";
					$liaison = " à ";
					break;
				default:
					$time = "";
					$liaison = " at ";
					break;
			}
			
			
			$format1 = bii_multilingual_date_format()["shortdays"];
			$format2 = bii_multilingual_date_format()["hour"];
			
			$time .= $dt->format($format1).$liaison.$dt->format($format2);
		}
	}
	return $time;
}

if (get_option("bii_use_multilingual") && get_option("bii_useclasses")) {
	add_filter("um_activity_human_post_time", "bii_multilingual_date");

	add_filter("bii_multilingual_default_language_selection_admin_script", "bii_multilingual_default_language_selection_admin_script");
	add_filter("bii_multilingual_default_language_admin_selection", "bii_multilingual_default_language_admin_selection");
	add_filter("bii_multilingual_real_baseurl", "bii_multilingual_real_baseurl", 10, 2);
	add_filter("bii_multilingual_display_flag", "bii_multilingual_display_flag");
	add_filter("bii_multilingual_select_flags", "bii_multilingual_select_flags");
	add_action("bii_options_submit", "bii_multilingual_option_submit", 5);

	add_action("bii_additionnal_js_var", "bii_multilingual_additionnal_js_var");
	add_action('wp_enqueue_scripts', "bii_multilingual_enqueueJS");
	add_action('wp_enqueue_scripts', "bii_multilingual_css_front");
	add_action("bii_options_title", "bii_add_multilingual_option_title", 10);
	add_action("bii_options", "bii_add_multilingual_options");
//
	add_filter("body_class", "bii_multilingual_body_class");
	add_action("bii_after_include_class", "bii_multilingual_include_classes", 11);

	add_shortcode("bii_autotranslate", "bii_multilingual_adaptative_text");

	add_action("bii_specific_shortcodes", "bii_multilingual_list_shortcodes");

	add_action("gettext", "bii_multilingual_more_translation");

//	add_filter('wp_nav_menu_items', 'bii_nav_menu_items', 10, 2);
	add_filter('bii_div_flags', 'bii_div_flags', 10, 1);

	add_filter('widget_title', "bii_multilingual_widget_titles");

	add_filter('bii_multilingual_filter_um_localize_permalink', "bii_multilingual_filter_um_localize_permalink", 10, 3);

	add_filter('date_i18n', 'bii_multilingual_filter_date_i18n', 10, 4);
	add_filter('bii_multilingual_link_selector_translation', 'bii_multilingual_link_selector_translation', 10, 2);
	add_filter('bii_multilingual_menuitem_class', 'bii_multilingual_menuitem_class', 10, 2);
	add_filter('yith_wcwl_socials_share_title', 'bii_yith_wcwl_socials_share_title', 10, 1);

	add_filter("bii_multilingual_current_language", "bii_multilingual_current_language");

	add_filter("bii_multilingual_date_format", "bii_multilingual_date_format");
	add_filter("nfum_dataTableOptions", "bii_multilingual_nfum_dataTableOptions");

	if (get_option("bii_use_um")) {
		add_filter("um_profile_menu_link_main", "bii_um_multilingual_fix_menu");
		add_filter("um_profile_menu_link_activity", "bii_um_multilingual_fix_menu");
		add_filter("um_profile_menu_link_posts", "bii_um_multilingual_fix_menu");
		add_filter("um_profile_menu_link_comments", "bii_um_multilingual_fix_menu");
		add_filter("um_profile_menu_link_messages", "bii_um_multilingual_fix_menu");

		add_filter("um_user_profile_tabs", "bii_um_multilingual_tabs");

		add_filter("um_view_label_user_registered", "bii_um_multilingual_label_title");
		add_filter("um_view_label_online_status", "bii_um_multilingual_label_title");
		add_filter("um_view_label_first_name", "bii_um_multilingual_label_title");
		add_filter("um_view_label_last_name", "bii_um_multilingual_label_title");
		add_filter("um_view_label_country", "bii_um_multilingual_label_title");
		add_filter("um_view_label_bii_cover", "bii_um_multilingual_label_title");
		add_filter("um_view_label_adresse", "bii_um_multilingual_label_title");
		add_filter("um_view_label_code_postal", "bii_um_multilingual_label_title");
		add_filter("um_view_label_ville", "bii_um_multilingual_label_title");
	}

	if (get_option('bii_use_gtranslate')) {
		add_filter("bii_multilingual_submenu_item", "bii_multilingual_submenu_item");
		remove_filter("bii_multilingual_menuitem_class", "bii_multilingual_menuitem_class");
		add_filter('bii_multilingual_menuitem_class', 'bii_multilingual_menuitem_class_wgtranslate', 10, 2);
	}
}