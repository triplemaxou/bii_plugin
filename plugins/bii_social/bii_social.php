<?php
/*
  Plugin Name: Bii Social
  Description: Ajoute des fonctionnalités avec Facebook
  Version: 0.1
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_social_version', '0.1');
define('bii_social_path', plugin_dir_path(__FILE__));
define('bii_social_url', plugin_dir_url(__FILE__));
define('bii_social_now', time());
define('bii_social_oneyearago', time() - 31556926);




add_action("bii_informations", function() {
	?>
	<tbody id="bii_bdd">
		<tr><th colspan="2">Bii_Social</th>
		<tr><td>Les options sociales pour facebook sont </td><td><?= bii_makebutton("bii_social_fb", 1, 1); ?></td></tr>
		<tr><td>Les options sociales pour twitter sont </td><td><?= bii_makebutton("bii_social_tw", 1, 1); ?></td></tr>
	</tbody>
	<?php
}, 12);

function bii_getFromGraph($page, $method, $start_date, $end_date, $path = null, $limit = null, $fields = null) {
	$token = bii_getFbToken();
	$pathstring = "";
	if ($start_date > $end_date) {
		$d = $end_date;
		$end_date = $start_date;
		$start_date = $d;
	}
	if ($start_date != null) {
		$start_date = "&since=$start_date";
	}
	if ($end_date != null) {
		$end_date = "&until=$end_date";
	}

	if ($path != null) {
		$pathstring = "/" . urlencode($path) . "/";
	}
	if ($limit != null) {
		$limit = "&limit=$limit";
	}
	if ($fields != null) {
		$fields = "&fields=$fields";
//		$fields = "&fields=id,name,message,story,likes.limit(1).summary(true),shares,picture";
	}

	$url = "https://graph.facebook.com/" . urlencode($page) . "$pathstring?access_token=$token$start_date$end_date$limit$fields";
	pre($url, "purple");
//	echo $url ."<br />";
	$data = json_decode(file_get_contents($url));

	if (!isset($data->$method)) {
		return $data;
	}

	return $data->$method;
}

function bii_social_SC_facebook_feed($args = [], $content = "") {
	$app_id = get_option("bii_social_app_id_page");
	$height = 500;
	$width = "";
	if (isset($args["app_id"])) {
		$app_id = $args["app_id"];
	}
	if (isset($args["height"])) {
		$height = $args["height"];
	}
	if (isset($args["width"])) {
		$width = $args["width"];
	}
	$dataheight = "";
	$datawidth = "";
	if ($height) {
		$dataheight = "data-height='$height'";
	}
	if ($width) {
		$datawidth = "data-width='$width'";
	}
	?>
	<div class="fb-page" data-href="https://www.facebook.com/<?= $app_id; ?>" data-tabs="timeline" <?= $dataheight . $datawidth ?> data-small-header="true" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="true">
		<blockquote cite="https://www.facebook.com/<?= $app_id; ?>" class="fb-xfbml-parse-ignore">
			<a href="https://www.facebook.com/<?= $app_id; ?>"><?php bloginfo("name"); ?></a>
		</blockquote>
	</div>
	<?php
}

function bii_social_add_option_title() {
	?>
	<li role="presentation" class="hide-relative" data-relative="pl-social"><i class="fa fa-facebook"></i> Social</li>
	<?php
}

function bii_social_options() {
	?>
	<div class="col-xxs-12 pl-social bii_option hidden">
		<?= bii_makestuffbox("bii_social_app_id_page", "Id page Facebook", "text", "col-xxs-4"); ?>
		<?= bii_makestuffbox("bii_social_app_id", "App Id Facebook", "text", "col-xxs-4"); ?>
		<?= bii_makestuffbox("bii_social_app_secret", "App Secret Facebook", "text", "col-xxs-4"); ?>

	</div>
	<?php
}

function bii_social_option_submit() {
	$tableaucheck = ["bii_social_app_id_page", "bii_social_app_id", "bii_social_app_secret"];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}

function bii_social_enqueue_script() {
	
}

function bii_social_after_body() {
	global $post;
	if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bii_fb_feed')) {
		$app_id = get_option("bii_social_app_id");
		$lang = "fr_FR";
		if (function_exists("bii_multilingual_current_language")) {
			$lang = bii_multilingual_current_language();
			if ($lang == "fr") {
				$lang = "fr_FR";
			}
			if ($lang == "en") {
				$lang = "en_GB";
			}
		}
		?>
		<div id="fb-root"></div>
		<script>(function (d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id))
					return;
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/<?= $lang; ?>/sdk.js#xfbml=1&version=v2.6&appId=<?= $app_id; ?>";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
		<?php
	}
}

function bii_social_list_shortcodes() {
	if (get_option("bii_social_fb")) {
		?>
		<tr>
			<td>
				<ul>
					<li><strong>[bii_fb_feed (height="X")]</strong></li>
				</ul>			 
			</td>
			<td>Appelle le feed facebook, height permet de choisir une hauteur (en px, défaut 500)</td>
		</tr>
		<?php
	}
}

function bii_getPostsFacebook($page, $start_date = bii_social_oneyearago, $end_date = bii_social_now, $limit = null) {
	$method = "data";
	return bii_getFromGraph($page, $method, $start_date, $end_date, "posts", $limit, "id,name,message,link,object_id,created_time");
}

function bii_getPostFacebook($page, $start_date = bii_social_oneyearago, $end_date = bii_social_now) {
	$method = "data";
	return bii_getFromGraph($page, $method, $start_date, $end_date, null, null, "id,created_time,name,message,story,likes.limit(1).summary(true),shares");
}

function bii_getPhotoFacebook($page, $start_date = bii_social_oneyearago, $end_date = bii_social_now) {
	$method = "data";
	return bii_getFromGraph($page, $method, $start_date, $end_date, null, null, "id,created_time,album,images.limit(1)");
}

function bii_getAlbumFacebook($page, $start_date = bii_social_oneyearago, $end_date = bii_social_now) {
	$method = "data";
	return bii_getFromGraph($page, $method, $start_date, $end_date, "photos", 100, "id,album,images.limit(1)");
}

function bii_fetchUrl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);

	$feedData = curl_exec($ch);
	curl_close($ch);

	return $feedData;
}

function bii_getFbToken($app_id = null, $app_secret = null) {
	if ($app_id == null) {
		$app_id = get_option("bii_social_app_id");
	}
	if ($app_secret == null) {
		$app_secret = get_option("bii_social_app_secret");
	}

	$token = $app_id . '|' . $app_secret;
	return $token;
}

function bii_fbDateToTimestamp($string) {
	$expl1 = explode("+", $string);
	$expl2 = explode("T", $expl1[0]);
	$explDate = explode("-", $expl2[0]);
	$explHour = explode(":", $expl2[1]);
	$year = $explDate[0];
	$month = $explDate[1];
	$day = $explDate[2];
	$hour = $explHour[0];
	$minute = $explHour[1];
	$second = $explHour[2];
	return mktime($hour, $minute, $second, $month, $day, $year);
}

function bii_social_import_feed_facebook() {
	$postsfb = bii_getPostsFacebook("armonycoiffure.isneauville");
	$albums = [];
	$i = 1;
	foreach ($postsfb as $postfb) {
		if ($postfb->object_id) {
			$photo = bii_getPhotoFacebook($postfb->object_id, null, null);
			$image = $photo->images[0];
			unset($photo->images);
			$album = $photo->album;
			$albums[$album->id] = $album->name;

			$postfb->album = $album;
			$postfb->image = $image;

			pre($postfb, "blue");

			$id_fb = $postfb->id;


			$nb = postmeta::nb("meta_key = 'id_graph' and meta_value = '$id_fb'");



			if ($nb == 0) {
				$tmstp = bii_fbDateToTimestamp($postfb->created_time);
				$date = date("Y-m-d H:i:s", $tmstp);
				$datefr = date("d/m/Y", $tmstp);
				$datefile = date("d-m-Y-h-i-s", $tmstp);
				$postarr = [
					"post_date" => $date,
					"post_title" => "Actualité Facebook du $datefr",
					"post_author" => "1",
					"post_status"=>"publish",
					"meta_input" => ["id_graph" => $id_fb,"link-fb"=>$postfb->link],
				];
				if($postfb->message){
					$postarr["post_content"]= $postfb->message;
				}
				$id_post = wp_insert_post($postarr);
				wp_set_post_terms($id_post, [5], 'category', true);
				$upload_dir = wp_upload_dir()["path"];
				$upload_adress = $upload_dir . "/fb-import-$datefile.png";
				pre($upload_adress,"green");
				$data = file_get_contents($image->source);
				file_put_contents($upload_adress, $data);
				$filename = $upload_adress;
				pre($filename,"green");

				// The ID of the post this attachment is for.
				$parent_post_id = $id_post;

				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype(basename($filename), null);

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();
				$guid = $wp_upload_dir['url'] . '/' . basename($filename);

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid' => $guid,
					'post_mime_type' => $filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
					'post_content' => '',
					'post_status' => 'inherit'
				);

				// Insert the attachment.
				$attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);


				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
				wp_update_attachment_metadata($attach_id, $attach_data);
				set_post_thumbnail($parent_post_id, $attach_id);

				++$i;
			}
		} else {
			pre($postfb, "red");
		}
		/*
		  ?>
		  <a href="<?= $event->link; ?>">
		  <figure id="<?= $event->id; ?>">
		  <img src="<?= $photo->images[0]->source; ?>" />
		  <figcaption><?= $event->message; ?></figcaption>
		  </figure>
		  </a>
		  <?php
		  // */
	}
	pre($albums, "purple");
}

if (get_option("bii_social_fb") || get_option("bii_social_tw")) {
	if (get_option("bii_social_fb")) {

		add_action("kleo_after_body", "bii_social_after_body");
		add_action("wpex_hook_main_top", "bii_social_after_body");
	}
	if (get_option("bii_social_tw")) {
		
	}
	add_action("bii_options_submit", "bii_social_option_submit", 5);

	add_action("bii_options_title", "bii_social_add_option_title", 10);
	add_action("bii_options", "bii_social_options");

	add_shortcode("bii_fb_feed", "bii_social_SC_facebook_feed");
	add_action("bii_specific_shortcodes", "bii_social_list_shortcodes");
}
