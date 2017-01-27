<?php
/*
  Plugin Name: Bii restricted content
  Description: Permet de restreindre l'affichage d'une page à une certaine liste d'utilisateurs
  Version: 1.2
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_restricted_content_version', '1.2');

add_action('save_post', 'bii_RC_save_metaboxes');

function bii_RC_save_metaboxes($post_ID) {
	$array_values = ["bii_userestrict_content", "bii_users_allowed"];
	foreach ($array_values as $val) {
		if (isset($_POST[$val])) {
			if (is_array($_POST[$val])) {
				bii_write_log($_POST[$val]);
				update_post_meta($post_ID, $val, serialize($_POST[$val]));
			} else {
				update_post_meta($post_ID, $val, esc_html($_POST[$val]));
			}
		}
	}
}

function bii_MB_restrict_user($post) {
	if (get_option("bii_userestrict_content")) {
		bii_write_log("bii_userestrict_content");
		bii_write_log(plugins_url('js/jquery.multi-select.js', __FILE__));
		wp_enqueue_style('lou-multiselect-css', plugins_url('css/multi-select.css', __FILE__));
		wp_enqueue_script('lou-multiselect', plugins_url('js/jquery.multi-select.js', __FILE__), array('jquery'), false, true);

		$userc = get_post_meta($post->ID, 'bii_userestrict_content', true);
		$user_allowed = unserialize(get_post_meta($post->ID, 'bii_users_allowed', true));
		if (!$userc) {
			$userc = 0;
		}
		if (!$user_allowed) {
			$user_allowed = [];
		}
		$checked = "";
		$hidden = "hidden";
		if ($userc == 1) {
			$checked = "checked='checked'";
		}
		?>
		<style>
			.bii_label{
				display: inline-block;
				width: 77%;
			}
		</style>
		<input type='checkbox'  id='bii_userestrict_content-cbx' name='bii_userestrict_content-cbx' class='cbx-data-change form-control' data-change='bii_userestrict_content' <?= $checked ?> />
		<label class="bii_label" for="bii_userestrict_content-cbx">Utiliser la restriction de contenu</label>
		<input type='hidden'  id='bii_userestrict_content' name='bii_userestrict_content' value='<?= $userc; ?>' /><div class="bii_preloader <?= $hidden; ?>">
			<select multiple="multiple" name="bii_users_allowed[]" id="bii_users_allowed" class="multiselect">
				<?= users::genOptionForm("", $user_allowed); ?>
			</select>
		</div>

		<?php
	}
}

add_action('add_meta_boxes', 'bii_RC_metaboxes');

function bii_RC_metaboxes() {
	add_meta_box("bii_RCContent", "Masquer le contenu", "bii_MB_restrict_user", null, "side");
}

add_action("bii_informations", function() {
	?>
	<tbody id="bii_restricted_content">
		<tr><th colspan="2">Bii_restricted_content</th>
		<tr><td>La fonctionnalité de restriction de contenu est </td><td><?= bii_makebutton("bii_userestrict_content", 0, 1); ?></td></tr>
	</tbody>
	<?php
});

function bii_restrict_post($content) {
	$restricted = false;
	if (get_option("bii_userestrict_content")) {
//	bii_write_log($content);
		$id_post = 0;

		if (isset($GLOBALS['post']->ID)) {
			$id_post = $GLOBALS['post']->ID;
			$metas = get_post_meta($id_post);
//		bii_write_log($metas);
			if (isset($metas["bii_userestrict_content"]) && $metas["bii_userestrict_content"][0] == 1) {
				rocket_clean_post($id_post);
				$restricted = true;
				bii_write_log($metas["bii_users_allowed"][0]);
				if (isset($metas["bii_users_allowed"])) {
					$ua = unserialize(get_post_meta($id_post, 'bii_users_allowed', true));
					bii_write_log('$ua');
					bii_write_log($ua);
					$user_id = get_current_user_id();

					if (in_array($user_id, $ua)) {
						$restricted = false;
					}
				}
			}
		}
	}
	if ($restricted) {
		$content = apply_filters("bii_rc_text", wp_login_form(["echo" => false]));
//		$content = "<p>Ce contenu n'est accessible qu'à certains utilisateurs, veuillez vous connecter via le formulaire ci dessous </p>" . wp_login_form(["echo" => false]);
		return $content;
	} else {
		return $content;
	}
}

add_filter('the_content', 'bii_restrict_post');

function bii_rc_text($content) {
	if (!get_option("bii_rc_text_before")) {
		update_option("bii_rc_text_before", "<p>Ce contenu n'est accessible qu'à certains utilisateurs, veuillez vous connecter via le formulaire ci dessous </p>");
	}
	if (!get_option("bii_rc_text_after")) {
		update_option("bii_rc_text_after", "<p></p>");
	}
	return stripcslashes(get_option("bii_rc_text_before")) . $content . stripcslashes(get_option("bii_rc_text_after"));
}

add_filter('bii_rc_text', "bii_rc_text");

if (get_option("bii_userestrict_content")) {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative" data-relative="pl-userestrict"><i class="fa fa-eye-slash"></i> Contenu caché</li>
		<?php
	});
	add_action("bii_options", function() {
		?>
		<div class="hidden col-xxs-12 pl-userestrict bii_option">
			<?php
			bii_makestuffbox("bii_rc_text_before", "Message avant le formulaire", "textarea");
			bii_makestuffbox("bii_rc_text_after", "Message après le formulaire", "textarea");
			?>
		</div>
		<?php
	});
}

add_action("bii_options_submit", function() {
	$tableaucheck = ["bii_rc_text_before", "bii_rc_text_after"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}, 5);

function bii_restricted_content_enqueueJS() {	
	wp_enqueue_script('restrict-admin', plugins_url('js/restrict_admin.js', __FILE__), array('jquery', 'util'), false, true);
}

add_action('admin_enqueue_scripts', "bii_restricted_content_enqueueJS");
