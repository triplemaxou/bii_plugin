<?php
/*
  Plugin Name: Bii_css
  Description: Ajoute bootstrap et font awesome sur le site et son back office
  Version: 1.6
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_css_version', '1.6');
define('bii_css_path', plugin_dir_path(__FILE__));
define('bii_css_url', plugin_dir_url(__FILE__));

if (get_option("bii_flyout") || get_option("bii_usepreloader")) {
	update_option("bii_afficher_menu_css", 1);
} else {
	update_option("bii_afficher_menu_css", 0);
}

function bii_css_enqueue_scripts() {
	if (get_option("bii_afficher_menu_css")) {
		wp_enqueue_script('jquery-effects-core');
	}
	if (get_option("bii_useleftmenu")) {
		wp_enqueue_style('leftmenu', plugins_url('css/leftmenu.css', __FILE__));
		wp_enqueue_script('leftmenuscript', plugins_url('js/leftmenu.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-effects-core', 'util'), false, true);
	}
	if (get_option("bii_flyout")) {
		wp_enqueue_style('bii_flyout', plugins_url('css/flyout.css', __FILE__));
		wp_enqueue_script('bii_flyoutscript', plugins_url('js/flyout.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-effects-core', 'util'), false, true);
	}
	if (get_option("bii_usebootstrap_front")) {
		wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.css', __FILE__));
	}
	if (get_option("bii_fa_front")) {
		wp_enqueue_style('font-awesome', plugins_url('css/font-awesome.min.css', __FILE__));
	}
}

function bii_css_admin_enqueue_scripts() {
	if (isset($_GET["page"]) && (strpos($_GET["page"], "bii") !== false) || (strpos($_GET["page"], "_list") !== false) || (strpos($_GET["page"], "_edit") !== false) || (strpos($_GET["page"], "_edit") !== false)) {
		if (get_option("bii_usebootstrap_admin")) {
			wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.css', __FILE__));
		}
//		if (get_option("bii_usebootstrap_admin_js")) {
//			wp_enqueue_script('bootstrapjs', plugins_url('js/bootstrap.min.js', __FILE__), array('jquery'), false, true);
//		}
		if (get_option("bii_fa_admin")) {
			wp_enqueue_style('font-awesome', plugins_url('css/font-awesome.min.css', __FILE__));
		}
//	wp_enqueue_style('bootstrap-theme', plugins_url('css/bootstrap-theme.css', __FILE__));
	}
}

function bii_css_info() {
	?>
	<tbody id="bii_css">
		<tr><th colspan="2">Bii_CSS</th>
	<!--		<tr><td>Le menu à gauche est </td><td><?= bii_makebutton("bii_useleftmenu"); ?></td></tr>-->
		<tr><td>Le menu "application mobile" est </td><td><?= bii_makebutton("bii_menu_appli"); ?></td></tr>
		<?php if (get_option("bii_menu_appli")) { ?>
			<tr><td>Le menu à gauche "application mobile" est </td><td><?= bii_makebutton("bii_menu_appli_left"); ?></td></tr>
			<tr><td>Le menu à droite "application mobile" est </td><td><?= bii_makebutton("bii_menu_appli_right"); ?></td></tr>
		<?php } ?>		
		<tr><td>Le flyout est </td><td><?= bii_makebutton("bii_flyout"); ?></td></tr>
		<tr><td>Bootstrap Admin est  </td><td><?= bii_makebutton("bii_usebootstrap_admin"); ?></td></tr>
		<!--<tr><td>Bootstrap Admin JS est  </td><td><?php // bii_makebutton("bii_usebootstrap_admin_js");                                   ?></td></tr>-->
		<tr><td>Font Awesome Admin est  </td><td><?= bii_makebutton("bii_fa_admin"); ?></td></tr>
		<tr title='Activer cette option peut "casser le site"'>
			<td><i class="fa fa-exclamation-triangle"></i> Bootstrap Front est  </td><td><?= bii_makebutton("bii_usebootstrap_front"); ?></td>
		</tr>
		<tr><td>Font Awesome Front est  </td><td><?= bii_makebutton("bii_fa_front"); ?></td></tr>
		<tr><td>Le preloader est  </td><td><?= bii_makebutton("bii_usepreloader"); ?></td></tr>
	</tbody>
	<?php
}

function bii_css_options_title() {
	?>
	<li role="presentation" class="hide-relative" data-relative="pl-css"><i class="fa fa-css3"></i> CSS</li>
	<?php
}

function bii_css_options() {
	?>
	<div class="hidden col-xxs-12 pl-css bii_option">
		<?php
		if (get_option("bii_flyout")) {
			bii_makestuffbox("bii_flyout_title", "Texte du flyout enroulé", "text", "col-xs-12 col-sm-6");
			bii_makestuffbox("bii_flyout_bgcolor", "Couleur de fond flyout enroulé", "text", "col-xs-12 col-sm-3");
			bii_makestuffbox("bii_flyout_color", "Couleur de texte flyout enroulé", "text", "col-xs-12 col-sm-3");
			bii_makestuffbox("bii_flyout_text", "HTML à afficher (flyout)", "textarea");
		}
		if (get_option("bii_usepreloader")) {
			bii_makestuffbox("bii_preloader_text", "HTML à afficher (preloader)", "textarea");
		}
		?>
	</div>
	<?php
}

function bii_css_options_submit() {
	$tableaucheck = ["bii_flyout_title", "bii_preloader_text", "bii_flyout_text", "bii_flyout_bgcolor", "bii_flyout_color"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}

add_action('admin_enqueue_scripts', "bii_css_admin_enqueue_scripts");
add_action('wp_enqueue_scripts', "bii_css_enqueue_scripts");


if (get_option("bii_afficher_menu_css")) {
	add_action("bii_options_title", "bii_css_options_title");
	add_action("bii_options", "bii_css_options");
}
add_action("bii_options_submit", "bii_css_options_submit", 5);
add_action("bii_informations", "bii_css_info");

add_filter("bii_class_menu", function($arg1, $arg2) {
	$class = "";
	if (get_option("bii_useleftmenu")) {
		$class.="bii-left-menu";
	}
	return $class;
}, 10, 2);
add_action("between_header_and_containerwrapper", function() {
	?>
	<div id="bii-overlay"></div>
	<?php
}, 10, 2);

//<editor-fold desc="bii_menu_appli">

function bii_display_menu_appli_option_title() {
	?>
	<li role="presentation" class="hide-relative" data-relative="pl-menu-appli"><i class="fa fa-bars"></i> Menu appli</li>
	<?php
}

function bii_display_menu_appli_options() {
	if (!get_option("bii_menu_appli_number")) {
		update_option("bii_menu_appli_number", "4");
	}
	$nb = get_option("bii_menu_appli_number");
	?>
	<div class="col-xxs-12 pl-menu-appli bii_option hidden">		
		<?php
		echo bii_makestuffbox("bii_menu_appli_number", "Nombre d'éléments", "number", "col-xxs-12");
		for ($i = 1; $i <= $nb; ++$i) {
			echo bii_makestuffbox("bii_menu_appli_link_$i", "Lien de l'élément $i", "text", "col-xxs-6");
			echo bii_makestuffbox("bii_menu_appli_name_$i", "Texte de l'élément $i", "text", "col-xxs-6");
		}
		?>
	</div>
	<?php
}

function bii_display_menu_option_submit() {
	update_option("bii_menu_appli_number", $_POST["bii_menu_appli_number"]);
	$nb = get_option("bii_menu_appli_number");
	$tableaucheck = [];
	for ($i = 1; $i <= $nb; ++$i) {
		$tableaucheck[] = "bii_menu_appli_link_$i";
		$tableaucheck[] = "bii_menu_appli_name_$i";
	}
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}

function bii_build_menu_appli($nb_cols = 4) {
	$nb_container = "12";
	$class = "col-xs-12";
	if ($nb_cols == 2) {
		$class = "col-xs-6";
	}
	if ($nb_cols == 3) {
		$class = "col-xs-4";
	}
	if ($nb_cols == 4) {
		$class = "col-xs-3";
	}
	if ($nb_cols == 6) {
		$class = "col-xs-2";
	}
	if ($nb_cols == 12) {
		$class = "col-xs-1";
	}
	$current_page = get_permalink();
	$leftmenu = false;
	$rightmenu = false;
	if (get_option("bii_menu_appli_left")) {
		$leftmenu = true;
		$menu_left = wp_nav_menu(array(
			'theme_location' => 'primary',
			'depth' => 3,
			'container' => 'div',
			'container_class' => 'bii-menu-left',
			'menu_class' => 'nav navbar-nav',
			//'fallback_cb'       => 'kleo_walker_nav_menu::fallback',
			'fallback_cb' => '',
			'walker' => new kleo_walker_nav_menu(),
			'echo' => false
			)
		);
	}
	if (get_option("bii_menu_appli_right")) {
		$rightmenu = true;
		$menu_right = "";
	}
	if ($rightmenu || $leftmenu) {
		?><div id="bii-overlay"></div><?php
	}
	?>
	<div class="bii-menu-appli hidden-lg hidden-md">
		<?php
		if ($leftmenu) {
			?>
			<div class="container-bii-left-menu">
				<div class="appli-menu-item" data-toogle=".bii-custom-left-menu">
					<i class="fa fa-bars"></i>
				</div>
			</div>
			<?php
		}
		?>
		<div class="container-bii-menu">
			<?php
			for ($i = 1; $i <= $nb_cols; ++$i) {
				$href = apply_filters("bii_trad_link", get_option("bii_menu_appli_link_$i"));
				$text = do_shortcode(stripslashes(get_option("bii_menu_appli_name_$i")));
				$selected = "";
				if ($href == $current_page) {
					$selected = "current";
				}
				?>
				<div class="appli-menu-item <?= $class . " " . $selected ?>">
					<a class="appli-menu-item-title" href="<?= $href ?>">
						<?= $text ?>
					</a>
				</div>			
				<?php
			}
			?>
		</div>
		<?php
		if ($rightmenu) {
			?>
			<div class="container-bii-right-menu" >
				<div class="appli-menu-item" data-toogle=".bii-custom-right-menu" data-direction="right">
					<i class="fa fa-ellipsis-v"></i>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	if ($leftmenu) {
		$logo_path = sq_option_url('logo');
		$logo_path = apply_filters('kleo_logo', $logo_path);
		?>
		<div class="bii-custom-left-menu hidden-lg hidden-md">
			<strong class="logo">
				<a href="<?php echo home_url(); ?>">
					<?php if ($logo_path != '') { ?>
						<img id="logo_img" title="<?php bloginfo('name'); ?>" src="<?php echo $logo_path; ?>"
							 alt="<?php bloginfo('name'); ?>">
						 <?php } else { ?>
							 <?php bloginfo('name'); ?>
						 <?php } ?>
				</a>
			</strong>
			<?= $menu_left ?>
		</div>
		<?php
	}
	if ($rightmenu) {
		?>
		<div class="bii-custom-right-menu">
			<h3><?php _e("Who is Online"); ?></h3>
			<?php
			the_widget('um_online_users');
			if (is_user_logged_in()) {
				?>
				<h3><?php _e("Abonnés"); ?></h3>
				<?php
				the_widget('um_my_followers');
			}
			?>
		</div>
		<?php
	}
}

function bii_display_menu_appli() {
	$nbcols = get_option("bii_menu_appli_number");
	bii_build_menu_appli($nbcols);
}

function bii_display_menu_appli_enqueueJS() {
	wp_enqueue_script('bii_menu_appli', plugins_url('js/bii_menu_appli.js', __FILE__), array('jquery', 'util'), false, true);
}

if (get_option("bii_menu_appli")) {

	add_action("bii_hook_after_menu", "bii_display_menu_appli");

	add_action("bii_options_title", "bii_display_menu_appli_option_title", 10);
	add_action("bii_options", "bii_display_menu_appli_options");
	add_action("bii_options_submit", "bii_display_menu_option_submit", 10);

	add_action('wp_enqueue_scripts', "bii_display_menu_appli_enqueueJS");
}

//</editor-fold>
//<editor-fold desc="flyout">
function bii_flyoutdisplay() {
	?>
	<div class="bii-flyout vc_hidden-xs">
		<div class="bii-onglet" style="color:<?= get_option("bii_flyout_color") ?>;background-color:<?= get_option("bii_flyout_bgcolor") ?>;">
			<?= stripcslashes(get_option("bii_flyout_title")) ?>
		</div>
		<div class="bii-flyout-content">
			<?= stripcslashes(get_option("bii_flyout_text")) ?>
		</div>		
	</div>
	<?php
}

add_action("bii_flyoutdisplay", "bii_flyoutdisplay");
if (get_option("bii_flyout")) {
	add_action("wpex_hook_main_bottom", "bii_flyoutdisplay");
}

//</editor-fold>
//<editor-fold desc="preloader">
function biipreloader_enqueueJS() {
	if (get_option("bii_usepreloader")) {
		wp_enqueue_script('bii_preloader', plugins_url('js/preloader.js', __FILE__), array('jquery', 'util'), false, true);
		wp_enqueue_style('preloader', plugins_url('css/preloader.css', __FILE__));
	}
}

function bii_SC_preloader($atts) {
	$timeout = 1000;
	$fading = 1000;
	if (isset($atts["timeout"])) {
		$timeout = $atts["timeout"] * 1;
	}
	if (isset($atts["fading"])) {
		$fading = $atts["fading"] * 1;
	}
	ob_start();
	?>
	<div id="bii_preloader" data-timeout="<?= $timeout; ?>" data-fading="<?= $fading; ?>">
		<div class="text-preloader">
			<?= stripcslashes(get_option("bii_preloader_text")) ?>
		</div>


	</div>
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_preloader_metaboxes() {
	add_meta_box("biipreload", "Preloader", "bii_MB_preloader", ["post", "page"], "normal");
}

function bii_MB_preloader($post) {
	if (get_option("bii_usepreloader")) {
		$useprl = get_post_meta($post->ID, 'bii_usepreloader', true);
		$timeout = get_post_meta($post->ID, 'bii_preloadertimeout', true);
		$fading = get_post_meta($post->ID, 'bii_preloaderfading', true);
		if (!$useprl) {
			$useprl = 0;
		}
		if (!$fading) {
			$fading = 1000;
		}
		if (!$timeout) {
			$timeout = 1000;
		}
		$checked = "";
		$hidden = "hidden";
		if ($useprl == 1) {
			$checked = "checked='checked'";
			$hidden = "";
		}
		?>

		<label class="bii_label" for="bii_usepreloader-cbx">Utiliser le preloader</label>
		<input type='hidden'  id='bii_usepreloader' name='bii_usepreloader' value='<?= $useprl; ?>' />
		<input type='checkbox'  id='bii_usepreloader-cbx' name='bii_usepreloader-cbx' class='cbx-data-change form-control' data-change='bii_usepreloader' <?= $checked ?> />
		<div class="bii_preloader <?= $hidden; ?>">
			<hr/>
			<div>
				<label class="bii_label" for="bii_preloadertimeout">Délai avant début de fondu</label>
				<input name="bii_preloadertimeout" id="bii_preloadertimeout" type="number"  id="menu_order" value="<?= $timeout ?>">
			</div>
			<div>
				<label class="bii_label" for="bii_preloaderfading">Durée de l'animation de fondu</label>
				<input name="bii_preloaderfading" id="bii_preloaderfading" type="number"  id="menu_order" value="<?= $fading ?>">
			</div>
		</div>
		<style>
			.bii_label{
				display: inline-block;
				width: 17%;
			}
		</style>
		<script>
			jQuery(".cbx-data-change").on("click", function () {
				jQuery(".bii_preloader").removeClass("hidden");
				var id = jQuery(this).attr("data-change");

				console.log(id);
				var checked = jQuery(this).is(":checked");
				var value = 0;
				if (checked == true) {
					value = 1;

				}
				jQuery("#" + id).val(value);
				console.log(jQuery("#" + id));
			});
		</script>
		<?php
	}
}

function save_metaboxes($post_ID) {
	$array_values = ["bii_usepreloader", "bii_preloadertimeout", "bii_preloaderfading"];
	foreach ($array_values as $val) {
		if (isset($_POST[$val])) {
			update_post_meta($post_ID, $val, esc_html($_POST[$val]));
		}
	}
}

function bii_build_preloader($post_ID) {
//	consoleLog($post_ID);
	if (!$post_ID) {
		if (isset($_GET["preview_id"])) {
			$post_ID = $_GET["preview_id"];
		}
	}
	$useprl = get_post_meta($post_ID, 'bii_usepreloader', true) && get_option("bii_usepreloader");
	if ($useprl) {
		$timeout = get_post_meta($post_ID, 'bii_preloadertimeout', true);
		$fading = get_post_meta($post_ID, 'bii_preloaderfading', true);

		echo bii_SC_preloader(["timeout" => $timeout, "fading" => $fading]);
	}
}

function bii_css_arrange_script() {
	$handle = 'logooos_script';
	$list = 'enqueued';
	if (wp_script_is($handle, $list)) {
		wp_dequeue_script('logooos_script');

		wp_register_script('logooos_script_bii', bii_css_url . "js/logos.js", null, null, true);
		wp_enqueue_script('logooos_script_bii');
	}
}

function bii_css_init() {
	remove_shortcode('logooos', 'logooos_shortcode');

	add_shortcode("logooos", "bii_css_lazyload_logoos");
}

function bii_css_lazyload_logoos($atts, $content = null) {
	extract(shortcode_atts(array(
		'id' => '',
		'columns' => '5',
		'itemsheightpercentage' => '0.65',
		'backgroundcolor' => 'transparent',
		'layout' => 'grid',
		'num' => '-1',
		'category' => '-1',
		'orderby' => 'date',
		'order' => 'DESC',
		'marginbetweenitems' => '',
		'tooltip' => 'disabled',
		'responsive' => 'enabled',
		'grayscale' => 'disabled',
		'border' => 'disabled',
		'bordercolor' => 'transparent',
		'borderradius' => 'logooos_no_radius',
		'onclickaction' => 'openLink',
		'detailsarea_padding' => '30px',
		'detailsarea_bgcolor' => '#f6f6f6',
		'detailsarea_closebtncolor' => '#777777',
		'detailsarea_border' => 'enabled',
		'detailsarea_bordercolor' => '#dcdcdc',
		'detailsarea_logo' => 'enabled',
		'detailsarea_logoborder' => 'enabled',
		'detailsarea_logobordercolor' => '#dcdcdc',
		'detailsarea_logobgcolor' => 'transparent',
		'autoplay' => 'true',
		'slider_circular' => 'true',
		'transitioneffect' => 'scroll',
		'easingfunction' => 'quadratic',
		'scrollduration' => '1000',
		'pauseduration' => '900',
		'buttonsbordercolor' => '#DCDCDC',
		'buttonsbgcolor' => '#FFFFFF',
		'buttonsarrowscolor' => 'lightgray',
		'slider_pagination' => 'disabled',
		'slider_pagination_color' => '#999999',
		'hovereffect' => '',
		'hovereffectcolor' => '#DCDCDC',
		'titlefontfamily' => '',
		'titlefontcolor' => '#777777',
		'titlefontsize' => '15px',
		'titlefontweight' => 'bold',
		'textfontfamily' => '',
		'textfontcolor' => '#777777',
		'textfontsize' => '12px',
		'excerpttextlength' => '55',
		'listborder' => 'enabled',
		'listbordercolor' => '#DCDCDC',
		'listborderstyle' => 'dashed',
		'morelinktext' => '',
		'morelinktextcolor' => '',
		'pagination' => 'disabled',
		'pagination_border_style' => 'solid',
		'pagination_border_color' => '#DDDDDD',
		'pagination_bg_color' => 'transparent',
		'pagination_font_color' => '#777777',
		'pagination_font_size' => '14px',
		'pagination_font_family' => '',
		'pagination_current_font_color' => '#F47E00',
		'pagination_current_bg_color' => 'transparent',
		'pagination_current_border_color' => '#DDDDDD',
		'pagination_align' => 'center',
		'pagination_divider_style' => 'solid',
		'pagination_divider_color' => '#DDDDDD',
		'wpml_current_lang' => ''
			), $atts));

	$logooos_suppress_filters = false;

	// 	query posts

	if (function_exists('icl_object_id') && $wpml_current_lang != '') {
		global $sitepress;
		if (isset($sitepress)) {
			$sitepress->switch_lang($wpml_current_lang);
		}
	}

	if ($category != '-1' && $category != '0') {
		$logooos_suppress_filters = true;
	}

	$args = array('post_type' => 'logooo',
		'posts_per_page' => $num,
		'orderby' => $orderby,
		'order' => $order,
		'suppress_filters' => $logooos_suppress_filters);

	if ($category != '-1' && $category != '0') {
		$args['tax_query'] = array(array('taxonomy' => 'logooocategory', 'include_children' => false, 'field' => 'term_id', 'terms' => array_map('intval', explode(',', $category))));
	}

	if (($layout == 'list' || $layout == 'grid') && $pagination == 'enabled') {
		$logooos_current_page = isset($_GET['logooos_page']) ? logooos_test_query_var($_GET['logooos_page']) : 1;
		$args['paged'] = $logooos_current_page;
	}

	$logooos_query = new WP_Query($args);

	$html = '';

	if ($logooos_query->have_posts()) {

		// ======== Classes ======== //
		$classes = '';

		//layout
		if ($layout == 'grid') {
			$classes.='logooos_grid ';
		} else if ($layout == 'slider') {
			$classes.='logooos_slider ';
		} else if ($layout == 'list') {
			$classes.='logooos_list ';
		}

		//responsive
		if ($responsive == 'enabled') {
			$classes.='logooos_responsive ';
		}

		//tooltip
		if ($layout != 'list') {
			if ($tooltip == 'enabled') {
				$classes.='logooos_withtooltip ';
			}
		}

		//grayscale
		if ($grayscale == 'enabled') {
			$classes.='logooos_grayscale ';
		}

		//border
		if ($border == 'enabled') {
			$classes.='logooos_border ';
		} else {
			$classes.='logooos_no_border ';
		}

		//list border
		if ($listborder == 'enabled') {
			$classes.='logooos_listborder ';
		}

		//border radius
		$classes.=$borderradius . ' ';

		//hover effect
		$classes.=$hovereffect . ' ';

		//show details
		if ($onclickaction == 'showDetails') {
			$classes.='logooos_showdetails ';
		}



		// ======== Data ======== //

		$data = '';

		//columns
		if ($layout != 'list') {
			$data = 'data-columnsnum="' . $columns . '" ';
		}

		//margin between items
		if ($layout != 'list') {
			$data.='data-marginbetweenitems="' . $marginbetweenitems . '" ';
		}

		//items height percentage
		$data.='data-itemsheightpercentage="' . $itemsheightpercentage . '" ';

		//hover effect
		$data.='data-hovereffect="' . $hovereffect . '" ';

		//hover effect color
		$data.='data-hovereffectcolor="' . $hovereffectcolor . '" ';

		//border color
		$data.='data-bordercolor="' . $bordercolor . '" ';

		if ($layout == 'slider') {
			// autoplay
			$data.='data-autoplay="' . $autoplay . '" ';
			// autoplay
			$data.='data-circular="' . $slider_circular . '" ';
			// Transition Effect
			$data.='data-transitioneffect="' . $transitioneffect . '" ';
			//easing function
			$data.='data-easingfunction="' . $easingfunction . '" ';
			// scroll duration
			$data.='data-scrollduration="' . $scrollduration . '" ';
			// pause duration
			$data.='data-pauseduration="' . $pauseduration . '" ';
			// buttons border color
			$data.='data-buttonsbordercolor="' . $buttonsbordercolor . '" ';
			// buttons background color
			$data.='data-buttonsbgcolor="' . $buttonsbgcolor . '" ';
			// pagination
			$data.='data-pagination="' . $slider_pagination . '" ';
			// pagination buttons color
			$data.='data-paginationcolor="' . $slider_pagination_color . '" ';

			// buttons arrows color
			if ($buttonsarrowscolor == 'darkgray') {
				$data.='data-buttonsarrowscolor="logooos_darkgrayarrows" ';
			} else if ($buttonsarrowscolor == 'lightgray') {
				$data.='data-buttonsarrowscolor="logooos_lightgrayarrows" ';
			} else if ($buttonsarrowscolor == 'white') {
				$data.='data-buttonsarrowscolor="logooos_whitearrows" ';
			}
		}

		if ($onclickaction == 'showDetails') {
			$data.='data-detailspageurl=' . plugins_url('details_area.php', __FILE__) . ' ';
		}

		$html.='<div id="' . $id . '" class="logooos_container logooos_notready"><div class="logooos ' . $classes . '" ' . $data . ' >';




		$detailsAreaStyle = '';
		$detailsAreaClass = '';
		$detailsArea_container_style = '';
		$detailsArea_logo_style = '';
		$detailsArea_closeBtn_style = '';

		$titleStyle = '';
		$textStyle = '';

		$detailsArea_html = '';

		if ($onclickaction == 'showDetails' || $layout == 'list') {

			// title style

			if ($titlefontfamily != '') {
				$titleStyle.='font-family:' . $titlefontfamily . '; ';
			}
			if ($titlefontcolor != '') {
				$titleStyle.='color:' . $titlefontcolor . '; ';
			}
			if ($titlefontsize != '') {
				$titleStyle.='font-size:' . $titlefontsize . '; ';
			}
			if ($titlefontweight != '') {
				$titleStyle.='font-weight:' . $titlefontweight . '; ';
			}


			// text style

			if ($textfontfamily != '') {
				$textStyle.='font-family:' . $textfontfamily . '; ';
			}
			if ($textfontcolor != '') {
				$textStyle.='color:' . $textfontcolor . '; ';
			}
			if ($textfontsize != '') {
				$textStyle.='font-size:' . $textfontsize . '; ';
			}
		}

		if ($onclickaction == 'showDetails') {

			// Details Area Style

			if ($marginbetweenitems != '') {
				$detailsAreaStyle .= 'margin:' . floor($marginbetweenitems / 2) . 'px;';
			}

			// Details Area Class

			if ($borderradius != '') {
				$detailsAreaClass .= $borderradius . ' ';
			}

			if ($detailsarea_logo == 'disabled') {
				$detailsAreaClass .= 'logooos_withoutLogo ';
			}

			// Details Area Container Style

			if ($detailsarea_bgcolor != '') {
				$detailsArea_container_style .= 'background-color:' . $detailsarea_bgcolor . ';';
			}

			if ($detailsarea_border == 'enabled' && $detailsarea_bordercolor != '') {
				$detailsArea_container_style .= 'border: 1px solid ' . $detailsarea_bordercolor . ';';
			}

			if ($detailsarea_padding != '') {
				$detailsArea_container_style .= 'padding: ' . $detailsarea_padding . ';';
			}

			if ($itemsheightpercentage != '' && $detailsarea_logo == 'enabled') {
				$detailsArea_container_style .= 'min-height: ' . (200 * $itemsheightpercentage) . 'px;';
			}


			// Details Area Logo Style

			if ($detailsarea_logoborder == 'enabled' && $detailsarea_logobordercolor != '') {
				$detailsArea_logo_style .= 'border: 1px solid ' . $detailsarea_logobordercolor . ';';
			}

			if ($detailsarea_logobgcolor != '') {
				$detailsArea_logo_style .= 'background-color: ' . $detailsarea_logobgcolor . ';';
			}

			if ($itemsheightpercentage != '') {
				$detailsArea_logo_style .= 'height: ' . (200 * $itemsheightpercentage) . 'px;';
			}


			// Details Area Close Button Style
			if ($detailsarea_padding != '') {
				$detailsArea_closeBtn_style .= 'top: ' . $detailsarea_padding . ';';
				$detailsArea_closeBtn_style .= 'right: ' . $detailsarea_padding . ';';
			}


			$detailsArea_html = '<div class="logooos_detailsarea ' . $detailsAreaClass . '" style="' . $detailsAreaStyle . '">
								
										<a class="logooos_detailsarea_closeBtn" href="#" style="' . $detailsArea_closeBtn_style . '" >
											<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 15 15" enable-background="new 0 0 15 15" xml:space="preserve">
												<g>
													<rect x="6.97" y="-2.576" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -3.1068 7.5001)" fill="' . $detailsarea_closebtncolor . '" width="1.061" height="20.152"/>
													<rect x="6.97" y="-2.576" transform="matrix(-0.7069 -0.7073 0.7073 -0.7069 7.497 18.1068)" fill="' . $detailsarea_closebtncolor . '" width="1.061" height="20.152"/>
												</g>
											</svg>
										</a>
										
										<div class="logooos_detailsarea_container" style="' . $detailsArea_container_style . '">
											<div class="logooos_detailsarea_img" style="' . $detailsArea_logo_style . '"></div>
											<div class="logooos_detailsarea_title" style="' . $titleStyle . '" ></div>
											<div class="logooos_detailsarea_text" style="' . $textStyle . '" ></div>
										</div>
							
									</div>';

			if ($layout == 'list') {
				$html.=$detailsArea_html;
			}
		}


		$i = 0;

		while ($i < $logooos_query->post_count) {

			$post = $logooos_query->posts;
			$thumbnailsrc = "";
			$href = '';
			$link = '';
			$imgSize = '99%';
			$bgSize = '99%';
			$link_target = '_blank';



			if (get_post_meta($post[$i]->ID, 'link', true) != '' && $onclickaction == 'openLink') {

				$link = get_post_meta($post[$i]->ID, 'link', true);

				if (strpos($link, 'http://') === false && strpos($link, 'https://') === false && strpos($link, 'mailto:') === false) {
					$href = 'href="http://' . get_post_meta($post[$i]->ID, 'link', true) . '"';
				} else {
					$href = 'href="' . get_post_meta($post[$i]->ID, 'link', true) . '"';
				}
			}

			if (get_post_meta($post[$i]->ID, 'imageSize', true) != '') {
				$imgSize = get_post_meta($post[$i]->ID, 'imageSize', true);
				$bgSize = '-webkit-background-size: ' . $imgSize . '; -moz-background-size: ' . $imgSize . '; background-size: ' . $imgSize . ';';
			}

			// if has post thumbnail		
			if (has_post_thumbnail($post[$i]->ID)) {
//				pre(get_intermediate_image_sizes());
//				$thumbnailsrc = wp_get_attachment_image(get_post_meta($post[$i]->ID, '_thumbnail_id', true));
				$thumbnailsrc = wp_get_attachment_image(get_post_meta($post[$i]->ID, '_thumbnail_id', true), "clients-slider");
			}

			if (get_post_meta($post[$i]->ID, 'link_target', true) != '') {
				$link_target = get_post_meta($post[$i]->ID, 'link_target', true);
			}


			$html.='<div class="bii bii-logooos_item logooos_item" data-id="' . $post[$i]->ID . '" data-title="' . $post[$i]->post_title . '" style="background-color:' . $backgroundcolor . '; border-color:' . $bordercolor . '">
						<a rel="nofollow" ' . $href . ' target="' . $link_target . '">';

			if ($thumbnailsrc != '') {
				$html.='<img class="bii-image-logoos" data-original="' . $thumbnailsrc . '" title="" style="max-width:' . $imgSize . ' !important; max-height:' . $imgSize . ' !important;" alt="' . $post[$i]->post_title . '" />';
			}

			if ($hovereffect == 'effect2') {
				$html.='<span class="logooos_effectspan"></span>';
			}

			$html.='</a>';



			$html.='</div>';







			if ($layout == 'list') {



				// text container style 

				$textContainerStyle = '';

				if ($listborder == 'enabled') {

					if ($listbordercolor != '') {
						$textContainerStyle.='border-bottom-color:' . $listbordercolor . '; ';
					}
					if ($listborderstyle != '') {
						$textContainerStyle.='border-bottom-style:' . $listborderstyle . '; ';
					}
				}

				$html.='<div class="logooos_textcontainer" style="' . $textContainerStyle . '">
								<div class="logooos_title" style="' . $titleStyle . '">' . $post[$i]->post_title . '</div>
								<div class="logooos_text" style="' . $textStyle . '"><div>' . wp_trim_words(get_post_meta($post[$i]->ID, 'description', true), $excerpttextlength) . '</div>';

				if (($morelinktext != '' && get_post_meta($post[$i]->ID, 'link', true) != '' && $onclickaction == 'openLink') || ($onclickaction == 'showDetails' && $morelinktext != '')) {

					$linkColor = '';
					if ($morelinktextcolor != '') {
						$linkColor = 'color:' . $morelinktextcolor;
					}

					$html.= '<a rel="nofollow" ' . $href . ' target="' . $link_target . '" data-id="' . $post[$i]->ID . '" class="logooos_morelink" style="' . $linkColor . '" >' . $morelinktext . '</a>';
				}

				$html.= '	</div>
							</div>';
			}

			$i++;
		}

		if ($onclickaction == 'showDetails' && $layout == 'grid') {
			$html.=$detailsArea_html . '<div class="logooos_detailsarea_clear"></div>';
		}

		$html.='</div>';

		if ($layout == 'slider' && $slider_pagination == 'enabled') {

			$logooos_slider_pagination_style = '';
			switch ($marginbetweenitems) {
				case '5px':
					$logooos_slider_pagination_style.='padding:13px 2px 13px 2px;';
					break;
				case '10px':
					$logooos_slider_pagination_style.='padding:10px 5px 10px 5px;';
					break;
				case '15px':
					$logooos_slider_pagination_style.='padding:8px 7px 8px 7px;';
					break;
				case '20px':
					$logooos_slider_pagination_style.='padding:5px 10px 5px 10px;';
					break;
				case '25px':
					$logooos_slider_pagination_style.='padding:3px 12px 3px 12px;';
					break;
				case '30px':
					$logooos_slider_pagination_style.='padding:0px 15px 0px 15px;';
					break;
				default:
					$logooos_slider_pagination_style.='padding:15px 0px 15px 0px;';
			}

			$html.= '<div class="logooos_slider_pagination" style="' . $logooos_slider_pagination_style . '"></div>';
		}

		if ($onclickaction == 'showDetails' && $layout == 'slider') {
			$html.=$detailsArea_html;
		}

		$html.='</div>';


		// Pagination

		if (($layout == 'list' || $layout == 'grid') && $pagination == 'enabled') {

			$logooos_total_pages = $logooos_query->max_num_pages;

			$logooos_pagination_style = '';
			$logooos_paginationItem_style = '';
			$logooos_paginationCurrentItem_style = '';

			if ($pagination_border_style != '') {
				$logooos_paginationItem_style.= 'border-style:' . $pagination_border_style . ';';
				$logooos_paginationCurrentItem_style.= 'border-style:' . $pagination_border_style . ';';
			}

			if ($pagination_border_color != '') {
				$logooos_paginationItem_style.= 'border-color:' . $pagination_border_color . ';';
			}

			if ($pagination_bg_color != '') {
				$logooos_paginationItem_style.= 'background-color:' . $pagination_bg_color . ';';
			}

			if ($pagination_font_color != '') {
				$logooos_paginationItem_style.= 'color:' . $pagination_font_color . ';';
			}

			if ($pagination_font_size != '') {
				$logooos_pagination_style.= 'font-size:' . $pagination_font_size . ';';
			}

			if ($pagination_font_family != '') {
				$logooos_pagination_style.= 'font-family:' . $pagination_font_family . ';';
			}

			if ($pagination_current_font_color != '') {
				$logooos_pagination_style.= 'color:' . $pagination_current_font_color . ';';
			}

			if ($pagination_current_bg_color != '') {
				$logooos_paginationCurrentItem_style.= 'background-color:' . $pagination_current_bg_color . ';';
			}

			if ($pagination_current_border_color != '') {
				$logooos_paginationCurrentItem_style.= 'border-color:' . $pagination_current_border_color . ';';
			}

			if ($pagination_align != '') {
				$logooos_pagination_style.= 'text-align:' . $pagination_align . ';';
			}

			if ($pagination_divider_style != '') {
				$logooos_pagination_style.= 'border-top-style:' . $pagination_divider_style . ';';
			}

			if ($pagination_divider_color != '') {
				$logooos_pagination_style.= 'border-top-color:' . $pagination_divider_color . ';';
			}

			if ($layout == 'list') {
				if ($pagination_divider_style == 'none') {
					$logooos_pagination_style.='margin:15px 0 0 0;';
				} else {
					$logooos_pagination_style.='margin:30px 0 0 0;';
				}
			}


			if ($layout == 'grid') {

				if ($pagination_divider_style == 'none') {
					switch ($marginbetweenitems) {
						case '5px':
							$logooos_pagination_style.='padding:18px 2px 0 2px;';
							break;
						case '10px':
							$logooos_pagination_style.='padding:15px 5px 0 5px;';
							break;
						case '15px':
							$logooos_pagination_style.='padding:13px 7px 0 7px;';
							break;
						case '20px':
							$logooos_pagination_style.='padding:10px 10px 0 10px;';
							break;
						case '25px':
							$logooos_pagination_style.='padding:13px 12px 0 12px;';
							break;
						case '30px':
							$logooos_pagination_style.='padding:15px 15px 0 15px;';
							break;
						default:
							$logooos_pagination_style.='padding:20px 0px 0 0px;';
					}
				} else {
					switch ($marginbetweenitems) {
						case '5px':
							$logooos_pagination_style.='margin:13px 2px 0 2px;';
							break;
						case '10px':
							$logooos_pagination_style.='margin:10px 5px 0 5px;';
							break;
						case '15px':
							$logooos_pagination_style.='margin:8px 7px 0 7px;';
							break;
						case '20px':
							$logooos_pagination_style.='margin:10px 10px 0 10px;';
							break;
						case '25px':
							$logooos_pagination_style.='margin:13px 12px 0 12px;';
							break;
						case '30px':
							$logooos_pagination_style.='margin:15px 15px 0 15px;';
							break;
						default:
							$logooos_pagination_style.='margin:15px 0px 0 0px;';
					}
				}
			}


			$html.= '<div class="logooos_pagination" style="' . $logooos_pagination_style . '">' . paginate_links(array('base' => str_replace('#038;', '', str_replace(999999999, '%#%', esc_url(add_query_arg('logooos_page', '999999999#' . $id, html_entity_decode(get_permalink()))))), 'format' => '?paged=%#%', 'current' => $logooos_current_page, 'total' => $logooos_total_pages, 'before_page_number' => '<span class="logooos_pagination_currentItem" style="' . $logooos_paginationCurrentItem_style . '"></span><span class="logooos_pagination_item" style="' . $logooos_paginationItem_style . '">', 'after_page_number' => '</span>', 'prev_next' => true, 'prev_text' => __('<span class="logooos-fa logooos_pagination_item" style="' . $logooos_paginationItem_style . '"></span>'), 'next_text' => __('<span class="logooos-fa logooos_pagination_item" style="' . $logooos_paginationItem_style . '"></span>'))) . '</div>';
		}
	}

	return $html;
}

add_shortcode('bii_preloader', 'bii_SC_preloader');
add_action('add_meta_boxes', 'bii_preloader_metaboxes');
add_action('save_post', 'save_metaboxes');
add_action('wp_enqueue_scripts', "biipreloader_enqueueJS");

add_action('init', 'bii_css_init', 20);
add_action('wp_print_scripts', 'bii_css_arrange_script', 100);
//</editor-fold>
