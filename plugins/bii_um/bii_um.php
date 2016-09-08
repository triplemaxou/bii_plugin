<?php
/*
  Plugin Name: Bii UM
  Description: Ajoute des fonctionnalités pour Ultimate Member
  Version: 0.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_um_version', '0.1');
define('bii_um_path', plugin_dir_path(__FILE__));
define('bii_um_template_path', plugin_dir_path(__FILE__) . "templates/");
define('bii_um_url', plugin_dir_url(__FILE__));




add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">
		<tr><th colspan="2">Bii_um</th>
		<tr><td>Les options supplémentaires pour ultimate member sont </td><td><?= bii_makebutton("bii_use_um", 1, 1); ?></td></tr>
	</tbody>
	<?php
}, 12);

function bii_um_filter_query($unfiltered) {
	global $wp_query;
	$user_id = get_current_user_id();
	$in_user_id = implode(",", bii_get_followers_id($user_id));
	$in_user_id .= "," . $user_id;

	if (!$unfiltered) {
		$unfiltered = [
			"post_type" => "um_activity",
			"posts_per_page" => 10,
			"post_status" => [0 => "publish"],
		];
	}


	if ($wp_query->query_vars["pagename"] == "fil-actualites" || $wp_query->query_vars["pagename"] == "activity" || $wp_query->query_vars["bii_loadwall"] == true) {
//		pre($wp_query->query_vars,"violet");

		$array = posts::posts_by_biilink_agency_in_good_language_and_followed();
		$unfiltered["post__in"] = $array;
	} elseif (isset($wp_query->query_vars["profiletab"]) && $wp_query->query_vars["profiletab"] == "activity") {
		$unfiltered['meta_query'][] = array(
			'relation' => 'OR',
			array('key' => '_user_id',
				'value' => $user_id,
				'compare' => '='
			),
			array('key' => '_wall_id',
				'value' => $user_id,
				'compare' => '='
			),
		);
	} else {

//		pre($wp_query->query_vars["pagename"]);
		$array = posts::posts_not_in_lang();
		$unfiltered["post__not_in"] = $array;
	}
//
//	logQueryVars();
//	pre($unfiltered);
	return $unfiltered;
}

function bii_um_activity_load_wall() {
	global $ultimatemember, $um_activity, $wp_query;
	$wp_query->query_vars["bii_loadwall"] = true;

	$number = um_get_option('activity_posts_num');
	$offset = absint($_POST['offset']);
	$user_id = absint($_POST['user_id']);
	$user_wall = absint($_POST['user_wall']);
	$hashtag = isset($_POST['hashtag']) ? (string) $_POST['hashtag'] : '';
	$core_page = isset($_POST['core_page']) ? (string) $_POST['core_page'] : '';

	// Specific user only
	if ($user_wall) {

		ob_start();
		$array = posts::posts_by_biilink_agency_in_good_language_and_followed();
		$args = array(
			'user_id' => $user_id,
			'user_wall' => 1,
			'offset' => $offset,
			'core_page' => $core_page,
//			'post__in' => $array,
		);

		// Global feed
	} else {

		ob_start();
		$args = array(
			'user_id' => 0,
			'template' => 'activity',
			'mode' => 'activity',
			'form_id' => 'um_activity_id',
			'user_wall' => 0,
			'offset' => $offset
		);

		if (isset($hashtag) && $hashtag) {

			$args['tax_query'] = array(
				array(
					'taxonomy' => 'um_hashtag',
					'field' => 'slug',
					'terms' => array($hashtag)
				)
			);

			$args['hashtag'] = $hashtag;
		} else if ($um_activity->api->followed_ids()) {

			$args['meta_query'][] = array('key' => '_user_id', 'value' => $um_activity->api->followed_ids(), 'compare' => 'IN');
		}
	}

	$um_activity->shortcode->args = $args;
	$um_activity->shortcode->load_template('user-wall');

	die();
}

function bii_um_filter_array($array) {
	$newarray = [];
	foreach ($array as $item) {
		foreach ($item as $key => $val) {
			$newarray[] = $val;
		}
	}
	return $newarray;
}

function bii_get_followers_id($user_id) {
	global $ultimatemember, $um_followers;
	return apply_filters('bii_um_filter_followers', $um_followers->api->following($user_id));
}

function bii_get_following_id($user_id) {
	global $ultimatemember, $um_followers;
	return apply_filters('bii_um_filter_followers', $um_followers->api->followers($user_id));
}

function bii_um_user_id() {
	global $wp_query;
//	logQueryVars();
	if ($wp_query->query_vars["page_id"] == 383 && $_REQUEST["um_action"] == "edit") {
		?>
		<div class='bii-cut-and-paste' data-selector='h1.page-title'>		
			<ins>
				<i class="um-faicon-picture-o" title="<?php _e("Changez votre image de couverture"); ?>"></i>
			</ins>
		</div>
		<?php
	}
	if ($wp_query->query_vars["page_id"] == 383) {

		$cover = "http://wonderwomenworld.com/wp-content/uploads/2016/06/img_top_WWW_home.jpg";
		logQueryVars();
		$meta = get_user_meta(um_profile_id(), "bii_cover")[0];
		if ($meta) {
			$cover = $meta;
		}
		?>
		<div class='bii-changecover' data-cover='<?= $cover; ?>'>

		</div>
		<?php
	}
	?><?php
}

function bii_um_enqueueJS() {
	wp_enqueue_script('bii_um', bii_um_url . "js/bii_um.js", array('jquery', 'util'), false, true);
	wp_enqueue_script('flipclock-fix', bii_um_url . "js/flipclock.min.js", array('jquery', 'util'), false, true);
}

function bii_um_activity_publish() {
	global $ultimatemember, $um_activity;
	extract($_POST);

	$output['error'] = '';

	if (!is_user_logged_in())
		$output['error'] = __('You can not post as guest', 'um-activity');

	if ($_post_content == '' || trim($_post_content) == '') {
		if (trim($_post_img) == '') {
			$output['error'] = __('You should type something first', 'um-activity');
		}
	}

	if (!$output['error']) {
		$hashtags = "";
		if (isset($_POST["_hashtags"])) {
			$hashtags = " " . str_replace(",", " ", $_POST["_hashtags"]);
		}
		if ($_POST['_post_id'] == 0) {

			$args = array(
				'post_title' => '',
				'post_type' => 'um_activity',
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
			);

			if (trim($_post_content)) {
				$orig_content = trim($_post_content);
				$safe_content = wp_kses($_post_content . $hashtags, array(
					'br' => array()
				));

				// shared a link
				$shared_link = $um_activity->api->get_content_link($safe_content);
				if (isset($shared_link) && $shared_link && !$_post_img) {
					$safe_content = str_replace($shared_link, '', $safe_content);
				}

				$args['post_content'] = $safe_content;
			}

			$args = apply_filters('um_activity_insert_post_args', $args);

			$post_id = wp_insert_post($args);

			// shared a link
			if (isset($shared_link) && $shared_link && !$_post_img) {
				$output['link'] = $um_activity->api->set_url_meta($shared_link, $post_id);
			} else {
				delete_post_meta($post_id, '_shared_link');
			}

			$args['post_content'] = apply_filters('um_activity_insert_post_content_filter', $args['post_content'], get_current_user_id(), $post_id, 'new');

			wp_update_post(array('ID' => $post_id, 'post_title' => $post_id, 'post_name' => $post_id, 'post_content' => $args['post_content']));

			if (isset($safe_content)) {
				$um_activity->api->hashtagit($post_id, $safe_content);
				update_post_meta($post_id, '_original_content', $orig_content);
				$output['orig_content'] = stripslashes_deep($orig_content);
			}

			if (absint($_POST['_wall_id']) > 0) {
				update_post_meta($post_id, '_wall_id', absint($_POST['_wall_id']));
			}

			// Save item meta
			update_post_meta($post_id, '_action', 'status');
			update_post_meta($post_id, '_user_id', get_current_user_id());
			update_post_meta($post_id, '_likes', 0);
			update_post_meta($post_id, '_comments', 0);

			if ($_post_img) {
				$um_is_temp_image = um_is_temp_image($_post_img);
				$photo_uri = $ultimatemember->files->new_user_upload(get_current_user_id(), $um_is_temp_image, '_um_wall_img_upload');
				update_post_meta($post_id, '_photo', $photo_uri);
				$output['photo'] = $photo_uri;
			}

			$output['postid'] = $post_id;
			$output['content'] = $um_activity->api->get_content($post_id);
			$output['video'] = $um_activity->api->get_video($post_id);

			do_action('um_activity_after_wall_post_published', $post_id, get_current_user_id(), absint($_POST['_wall_id']));
		} else {

			// Updating a current wall post
			$post_id = absint($_POST['_post_id']);

			if (trim($_post_content)) {
				$orig_content = trim($_post_content);
				$safe_content = wp_kses($_post_content, array(
					'br' => array()
				));

				// shared a link
				$shared_link = $um_activity->api->get_content_link($safe_content);
				if (isset($shared_link) && $shared_link && !$_post_img) {
					$safe_content = str_replace($shared_link, '', $safe_content);
					$output['link'] = $um_activity->api->set_url_meta($shared_link, $post_id);
				} else {
					delete_post_meta($post_id, '_shared_link');
				}

				$safe_content = apply_filters('um_activity_update_post_content_filter', $safe_content, $um_activity->api->get_author($post_id), $post_id, 'save');

				$args['post_content'] = $safe_content;
			}

			$args['ID'] = $post_id;
			$args = apply_filters('um_activity_update_post_args', $args);

			// hash tag replies
			$args['post_content'] = apply_filters('um_activity_insert_post_content_filter', $args['post_content'], get_current_user_id(), $post_id, 'new');

			wp_update_post($args);

			if (isset($safe_content)) {
				$um_activity->api->hashtagit($post_id, $safe_content);
				update_post_meta($post_id, '_original_content', $orig_content);
				$output['orig_content'] = stripslashes_deep($orig_content);
			}

			if (trim($_post_img) != '') {

				$um_is_temp_image = um_is_temp_image($_post_img);

				if ($um_is_temp_image) {
					$photo_uri = $ultimatemember->files->new_user_upload(get_current_user_id(), $um_is_temp_image, '_um_wall_img_upload');
					update_post_meta($post_id, '_photo', $photo_uri);
					$output['photo'] = $photo_uri;
				} else {
					$output['photo'] = $_post_img;
				}
			} else {

				delete_post_meta($post_id, '_photo');
			}

			$output['postid'] = $post_id;
			$output['content'] = $um_activity->api->get_content($post_id);
			$output['video'] = $um_activity->api->get_video($post_id);

			do_action('um_activity_after_wall_post_updated', $post_id, get_current_user_id(), absint($_POST['_wall_id']));
		}

		// other output
		$output['permalink'] = add_query_arg('wall_post', $post_id, get_permalink($ultimatemember->permalinks->core['activity']));
	}

	$output = json_encode($output);
	if (is_array($output)) {
		print_r($output);
	} else {
		echo $output;
	}die;
}

function bii_um_SC_mosaic($args = [], $content = "") {
	$size = 96;
	$limit = 36;
	$numberline = 3;
	$class_add = "";
	$class_add_tile = "";
	$class_add_tile_content = "";
	$lazyload = true;
	$display_content_after_tile = 18;
	$displaylink = true;

	if (isset($args['limit'])) {
		$limit = $args['limit'];
	}
	if (isset($args['numberline'])) {
		$numberline = $args['numberline'];
	}
	if (isset($args['size'])) {
		$size = $args['size'];
	}
	if (isset($args['el_class'])) {
		$class_add = $args['el_class'];
	}
	if (isset($args['el_class_tile'])) {
		$class_add_tile = $args['el_class_tile'];
	}
	if (isset($args['el_class_content'])) {
		$class_add_tile_content = $args['el_class_content'];
	}
	if (isset($args['skiplink']) && $args['skiplink'] == "0") {
		$displaylink = false;
	}
//	if (isset($args['el_class_tile'])) {
//		$class_add_tile = $args['el_class_tile'];
//	}
//	if (isset($args['el_class_tile'])) {
//		$class_add_tile = $args['el_class_tile'];
//	}
	if (isset($args['lazyload']) && $args['lazyload'] == "0") {
		$lazyload = false;
	}
	if (isset($args['display_content_after_tile'])) {
		$display_content_after_tile = $args['display_content_after_tile'];
	}

//	pre($args, "violet");

	ob_start();
	include(bii_um_template_path . "member_mosaic.php");
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_um_avatar($avatar, $id_or_email, $size, $default, $alt, $lazyload = true) {

	if ($lazyload) {
		$avatar = str_replace("src=", "src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' data-original=", $avatar);
		$avatar = str_replace("func-um_user", "func-um_usef lazyquick", $avatar);
	}
	$avatar = str_replace('alt=""', "alt='$alt'", $avatar);

	return $avatar;
}

function bii_um_list_shortcodes() {
	?><tr>
		<td>
			<ul>
				<li><strong>[bii_um_mosaic]contenu[/bii_um_mosaic]</strong></li>
				<li><strong>[bii_um_mosaic limit="n" size="n" el_class="xxxx" el_class_tile="xxxx" el_class_add_tile_content="xxx" lazyload="1|0" display_content_after_tile="n" ]contenu[/bii_um_mosaic]</strong></li>
			</ul>			 
		</td>
		<td>Appelle la mosaique des photos des membres
			<ul>
				<li>display_content_after_tile : afficher le contenu après la tuile n (défaut 50)</li>
				<li>limit correspond au nombre de photos que l'on souhaite afficher (par défaut 100)</li>
				<li>size correspond à la taille ne pixels des photos à afficher (défaut 96)</li>
				<li>el_class correspond à la classe que l'on souhaite ajouter au conteneur de la mosaique</li>
				<li>el_class_tile correspond à la classe que l'on souhaite ajouter à chaque tuile utilisateur</li>
				<li>el_class_content correspond à la classe que l'on souhaite ajouter à chaque tuile utilisateur</li>
				<li>el_class_add_tile_content correspond à la classe que l'on souhaite ajouter à la tuile de contenu</li>
				<li>lazyload doit être égal à 0 si on souhaite désactiver cette option (défaut 1)</li>
			</ul>
		</td>
	</tr><?php
}

function bii_um_wall_can_view($can_view, $profile_id) {
	global $wp_query;
	if ($wp_query->query_vars["pagename"] == "global-activity" || $wp_query->query_vars["pagename"] == "fil-actualites-global") {
		$can_view = -1;
	}
//	pre($wp_query->query_vars,"violet");
//		pre($can_view,"green");


	return $can_view;
}

if (get_option("bii_use_um")) {
	add_filter("um_activity_wall_args", "bii_um_filter_query", 1, 10);
	add_filter("bii_um_filter_followers", "bii_um_filter_array", 1, 10);
	add_filter("bii_get_followers_id", "bii_get_followers_id", 1, 10);
	add_filter("bii_get_following_id", "bii_get_following_id", 1, 10);



	add_action('bii_specific_shortcodes', "bii_um_list_shortcodes");

	add_action('wp_enqueue_scripts', "bii_um_enqueueJS");
	add_action("kleo_before_main", "bii_um_user_id");


	add_action('wp_ajax_nopriv_um_activity_load_wall', 'bii_um_activity_load_wall');
	add_action('wp_ajax_um_activity_load_wall', 'bii_um_activity_load_wall');
	remove_action('wp_ajax_nopriv_um_activity_load_wall', 'um_activity_load_wall');
	remove_action('wp_ajax_um_activity_load_wall', 'um_activity_load_wall');


	remove_action('wp_ajax_nopriv_um_activity_publish', 'um_activity_publish');
	remove_action('wp_ajax_um_activity_publish', 'um_activity_publish');

	add_action('wp_ajax_nopriv_um_activity_publish', 'bii_um_activity_publish');
	add_action('wp_ajax_um_activity_publish', 'bii_um_activity_publish');

	add_shortcode('bii_um_mosaic', "bii_um_SC_mosaic");

	add_filter("bii_um_get_avatar", "bii_um_avatar", 10, 6);

	add_filter("um_wall_can_view", 'bii_um_wall_can_view', 11, 2);
//	if(get_option())
}