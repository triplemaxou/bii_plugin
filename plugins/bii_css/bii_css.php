<?php
/*
  Plugin Name: Bii_css
  Description: Ajoute bootstrap et font awesome sur le site et son back office
  Version: 1.5
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_css_version', '1.5');
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
		<!--<tr><td>Bootstrap Admin JS est  </td><td><?php // bii_makebutton("bii_usebootstrap_admin_js");                                 ?></td></tr>-->
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

add_shortcode('bii_preloader', 'bii_SC_preloader');
add_action('add_meta_boxes', 'bii_preloader_metaboxes');
add_action('save_post', 'save_metaboxes');
add_action('wp_enqueue_scripts', "biipreloader_enqueueJS");

//</editor-fold>
