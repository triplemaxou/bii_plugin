<?php

class bii_page_perso extends bii_item_page_perso {

	protected $id;
	protected $user_id_main;
	protected $user_id_satell;
	protected $post_id_main;
	protected $post_id_satell;
	protected $titre;
	protected $website;
	protected $phone;
	protected $phone2;
	protected $fax;
	protected $address;
	protected $country;
	protected $id_layout;
	protected $etat;
	protected $lang;

	static function getListeProprietesFormEdit() {
		$ar = [
//			"user_id_main"=>"Id de l'utilisateur site principal",
			"user_id_satell" => __("Id de l'utilisateur site satellite"),
//			"post_id_main"=>"Id du post site principal",
			"post_id_satell" => __("Id du post site satellite"),
//			"titre" => __("Title"),
//			"website" => __("Website"),
//			"phone" => __("Phone"),
//			"phone2" => __("Second Phone"),
//			"fax" => __("Fax"),
//			"address" => __("Address"),
//			"country" => __("Country"),
			"id_layout" => __("Choose a Layout"),
			"layout_form" => __("Layout"),
			"lang" => __("Lang"),
		];
		if (!is_admin()) {
			unset($ar["post_id_satell"]);
			unset($ar["titre"]);
		}
		return $ar;
	}

	function post_id($lang = "fr") {
		$nom_methode = "post_id";
		return $this->call_satell($nom_methode, $lang);
	}

	function user_id($lang = "fr") {
		$nom_methode = "user_id";
		return $this->call_satell($nom_methode, $lang);
	}

	static function get_page_perso($is_satell = true) {
		$user_id = get_current_user_id();
		$lang = apply_filters("bii_multilingual_current_language", "");
		$item = new static();
		if ($user_id) {
			$suff = "_satell";
			if (!$is_satell) {
				$suff = "_main";
			}
			$meth = "user_id$suff";
			$req = "$meth = $user_id AND lang = '$lang'";
			$nb = static::nb($req);
			if (!$nb) {

				$item->insert();
				$item->updateChamps($user_id, $meth);
				$item->updateChamps($lang, "lang");
			} else {
				$ids = static::all_id($req);
				$item = new static($ids[0]);
			}
		}
		return $item;
	}

	static function has_page_perso($user_id = 0,$is_satell = true) {
		if ($user_id == 0) {
			$user_id = get_current_user_id();
		}
		$lang = apply_filters("bii_multilingual_current_language", "");
		$suff = "_satell";
		if (!$is_satell) {
			$suff = "_main";
		}
		$meth = "user_id$suff";
		$meth2 = "post_id$suff";
		$req = "$meth = $user_id AND lang = '$lang' AND $meth2 > 1";
		return static::nb($req);
	}

	static function editable() {
		return true;
	}

	static function supprimable() {
		return false;
	}

	static function feminin() {
		return true;
	}

	static function nom_classe_admin() {
		return "Page pro";
	}

	function id_layout() {
		if (!$this->id_layout) {
			$this->id_layout = 1;
		}
		return $this->id_layout;
	}

	function phone_class_stuffIA() {
		return "vc_col-xxs-12 vc_col-xs-12 vc_col-sm-4";
	}

	function phone2_class_stuffIA() {
		return "vc_col-xxs-12 vc_col-xs-12 vc_col-sm-4";
	}

	function fax_class_stuffIA() {
		return "vc_col-xxs-12 vc_col-xs-12 vc_col-sm-4";
	}

	function address_class_stuffIA() {
		return "vc_col-xxs-12 vc_col-xs-12 vc_col-sm-6";
	}

	function country_class_stuffIA() {
		return "vc_col-xxs-12 vc_col-xs-12 vc_col-sm-6";
	}

	function user_id_satell_inputIA() {
		$value = $this->user_id_satell();
		if (is_admin()) {
			?>
			<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="user_id_satell_div">
				<h3>
					<label for="lang"><?= __("Id de l'utilisateur site satellite") ?></label>
				</h3>
				<div class="inside">
					<input id="user_id_satell" name="user_id_satell" value='<?= $value ?>' class='form-control'>					
					<p></p>
				</div>
			</div>
			<?php
		} else {
			if (!$value) {
				$value = get_current_user_id();
			}
			?>
			<input id="user_id_satell" name="user_id_satell" value='<?= $value ?>' type='hidden' >
			<?php
		}
	}

	function id_layout_inputIA() {
		$value = $this->id_layout();
		?>
		<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="id_layout_div">
			<h3>
				<label for="id_layout"><?= __("Choose a Layout") ?></label>
			</h3>
			<div class="inside">
				<select id="id_layout" name="id_layout" class="form-control" data-reload-ajax='.bii_inside_layout_wrapper'>
					<?= bii_page_perso_layout::genOptionForm("", $value); ?>
				</select>  
				<p></p>
			</div>
		</div>
		<?php
	}

	function country_inputIA() {
		$value = $this->country();
		?>
		<div class="stuffbox <?= $this->country_class_stuffIA(); ?> " id="country_div">
			<h3>
				<label for="country"><?= __("Country") ?></label>
			</h3>
			<div class="inside">
				<select id="country" name="country" class="form-control" >
					<?= countries::genOptionForm("", $value); ?>
				</select>  
				<p></p>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php
	}

	function lang_inputIA() {
		$value = $this->lang();
		if (!$value) {
			$value = apply_filters("bii_multilingual_current_language", null);
		}
		if (is_admin()) {
			?>
			<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="lang_div">
				<h3>
					<label for="lang"><?= __("Lang") ?></label>
				</h3>
				<div class="inside">
					<input id="lang" name="lang" value='<?= $value ?>' class='form-control'>					
					<p></p>
				</div>
			</div>
			<?php
		} else {
			?>
			<input id="lang" name="lang" value='<?= $value ?>' type='hidden' >
			<?php
		}
	}

	function getTradIdPost($lang, $is_satell = true) {
		$id = -1;
		$satell = "_satell";
		$userid = $this->user_id_satell;
		if (!$is_satell) {
			$satell = "_main";
			$userid = $this->user_id_main;
		}
		$req = "user_id$satell = '$userid' AND lang ='$lang'";
		$liste = $this->getSomething($req, "bii_page_perso", "array_items");
//		pre($liste, "red");
		if (count($liste)) {
			$id = $liste[0]->post_id();
		}
		return $id;
	}

	function getTrad($is_satell = true) {
		$satell = "_satell";
		$userid = $this->user_id_satell;
		if (!$is_satell) {
			$satell = "_main";
			$userid = $this->user_id_main;
		}

		$req = "user_id$satell = '$userid' AND lang NOT IN('$this->lang')";
//		pre($req);
		return $this->getSomething($req, "bii_page_perso", "array_items")[0];
	}

	function getTradInlang($lang) {
		$satell = "_satell";
		$userid = $this->user_id_satell;
		if (!$is_satell) {
			$satell = "_main";
			$userid = $this->user_id_main;
		}
		return $this->getSomething("user_id$satell = $userid AND lang ='$lang'", "bii_page_perso", "array_items")[0];
	}

	function getContents() {
		return $this->getSomething("id_page_perso = $this->id", "bii_page_perso_content", "array_items");
	}

	function getTimeline() {
		return $this->getSomething("id_page_perso = $this->id", "bii_page_perso_timeline", "array_items");
	}

	function getImage($type = "logo") {
		return $this->getSomething("id_page_perso = $this->id AND image_type = '$type'", "bii_page_perso_image", "array_items");
	}

	function getImages($type = "caroussel") {
		return $this->getImage($type);
	}

	function layout_form() {
		
	}

	function layout_form_inputIA() {
		$layoutform = $this->trad_layoutshortcode();
		?>
		<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="layout_div">
			<h3>
				<label for="layout"><?= __("Layout") ?></label>
			</h3>
			<div class="inside">
				<div class='bii_inside_layout_wrapper' >
					<?= do_shortcode($layoutform); ?>
				</div>
				<p></p>
			</div>
		</div>
		<?php
	}

	function trad_layoutshortcode() {
		$layout = new bii_page_perso_layout($this->id_layout());
		$layoutform = $layout->get_layoutform();
		$countppblock = substr_count($layoutform, "BII_PP_BLOCK_");
//		pre($countppblock);

		$layoutform = str_replace("<p>[BII_PP_TITLE]</p>", $this->makeTitleForm(), $layoutform);
		$layoutform = str_replace("[BII_PP_LOGO]", $this->makeLogoForm(), $layoutform);
		$layoutform = str_replace("[BII_PP_CAROUSSEL]", $this->makeCarousselForm(), $layoutform);
		$layoutform = str_replace("[BII_PP_TIMELINE]", $this->makeTimelineForm(), $layoutform);
		if ($countppblock) {
			for ($i = 0; $i <= $countppblock; ++$i) {
//				pre("[BII_PP_BLOCK_$i]");
				$layoutform = str_replace("[BII_PP_BLOCK_$i]", $this->makeContentForm($i), $layoutform);
			}
		}
		return $layoutform;
	}

	function makeTitleForm() {
		$titre = $this->titre();
		ob_start();
		?>
		<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="titre_div">
			<h3>
				<label for="titre"><?= __("Title") ?></label>
			</h3>
			<div class="inside">
				<input value="<?= $titre ?>" class="form-control " name="titre" id="titre">
				<p></p>
			</div>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	function makeCarousselForm_old() {
		$contents_array = $this->getImages();
		$count = count($contents_array);
		ob_start();
//		echo do_shortcode('[gravityform id="1" title="true" description="true"]');
		?>
		<div class="carroussel bii-container-add">
			<input type="hidden" class="counter" value="<?= $count ?>" />
			<div class="todupl hidden">
				<div class="bii-gravity">
					<?= do_shortcode('[gravityform id="3" title="false" description="false"]') ?>
				</div>
			</div>
			<?php
			$index = 0;
			foreach ($contents_array as $image) {
				$imagesrc = $image->image();
				?>
				<div class="stuffbox stuffboxin vc_col-xxs-12 vc_col-xs-12 " id="id_timeline_<?= $index; ?>_div">
					<button class="btn btn-danger bii-container-btn-del del_timeline"><i class="fa fa-times-circle"></i></button>
					<h3>
						<label for="id_timeline_<?= $index; ?>"><?= __("Image") . " $p" ?></label>
					</h3>

					<div class="inside bii-gravity">
						<img src="<?= $imagesrc ?>" />
						<?= do_shortcode('[gravityform id="4" title="false" description="false"]') ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php
				++$index;
			}
			?>
			<button class="btn btn-info bii-container-btn-add add_image" data-duplicate=".todupl"><i class="fa fa-plus-circle"></i><?= __("Ajouter une image dans le caroussel") ?></button>

		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	function makeCarousselForm() {
		$contents_array = $this->getImages();
//		pre($contents_array);
		$liste_id = "";
		$val = "";
		foreach ($contents_array as $item) {
			$postid = $item->post_id();
			if ($postid) {
				$liste_id[] = $item->post_id();
			}
		}
		if (is_array($liste_id)) {
			$val = implode(",", $liste_id);
		}
		$query_images_args = array(
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'post_status' => 'inherit',
			'posts_per_page' => - 1,
			'post__not_in' => $liste_id,
			'author' => $this->user_id(),
		);

		$query_images = new WP_Query($query_images_args);

		ob_start();
//		echo do_shortcode('[gravityform id="1" title="true" description="true"]');
		?>
		<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="carroussel_div">
			<input id="id_caroussel" name="id_caroussel" type="hidden" value="<?= $val ?>"/>
			<h3>
				<label><?= __("Caroussel") ?></label>
			</h3>
			<p><?= __("Selectionnez les images que vous souhaitez ajouter dans le caroussel") ?></p>
			<div class="carroussel">

				<div class="inside">
					<ul class="bii_sortable">
						<?php
						if (is_array($liste_id)) {
							foreach ($liste_id as $id) {
								$imagesrc = wp_get_attachment_image_src($id, 'thumbnail')[0];
								$class_selected = "";
								if (in_array($id, $liste_id)) {
									$class_selected = "selected";
								}
								?>
								<li  data-id="<?= $id ?>" class="attachment save-ready bii-li-caroussel <?= $class_selected; ?>">
									<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">

										<div class="thumbnail">
											<div class="valide">
												<span class="fa fa-check"></span>
											</div>
											<div class="centered">
												<img src="<?= $imagesrc ?>" >
											</div>

										</div>
									</div>
								</li>
								<?php
							}
						}
						?>
						<?php
						foreach ($query_images->posts as $image) {
							$imagesrc = wp_get_attachment_image_src($image->ID, 'thumbnail')[0];
							$class_selected = "";
							if (is_array($liste_id) && in_array($image->ID, $liste_id)) {
								$class_selected = "selected";
							}
							?>
							<li  data-id="<?= $image->ID ?>" class="attachment save-ready bii-li-caroussel <?= $class_selected; ?>">
								<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">

									<div class="thumbnail">
										<div class="valide">
											<span class="fa fa-check"></span>
										</div>
										<div class="centered">
											<img src="<?= $imagesrc ?>" >
										</div>

									</div>
								</div>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	function makeLogoForm() {
		$id_logo = 0;
		$images = $this->getImage();
		if (isset($images[0])) {
			$id_logo = $images[0]->post_id();
		}
		ob_start();
		?>
		<div class="stuffbox stuffboxin vc_col-xxs-12 vc_col-xs-12" id="id_timeline_todupl_div">
			<input type="hidden" id="id_logo" name="id_logo" value="<?= $id_logo; ?>">
			<h3>
				<label ><?= __("Logo") ?></label>
			</h3>
			<div id='dropdown_logo'></div>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	function makeTimelineForm() {
		$contents_array = $this->getTimeline();
		$count = count($contents_array);
		ob_start();
		?>
		<div class="timelines bii-container-add">
			<input type="hidden" class="counter" value="<?= $count ?>" />

			<?php
			bii_page_perso_timeline::emptyFormTodupl();
			$index = 0;
			foreach ($contents_array as $timeline) {
				$timeline->form_edit_front($index);
				++$index;
			}
			?>
			<div class="vc_col-xxs-12 vc_col-xs-12 bii_insertbefore">
				<button class="btn btn-info bii-container-btn-add add_timeline" data-duplicate=".todupl"><i class="fa fa-plus-circle"></i><?= __("Ajouter un chiffre commenté") ?></button>
			</div>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	function makeContentForm($index = 0) {
		$contents_array = $this->getContents();
		$value = "";
		if (isset($contents_array[$index])) {
			$value = utf8_encode($contents_array[$index]->contenu());
		}
		$p = $index + 1;
		ob_start();
		?>
		<div class="stuffbox vc_col-xxs-12 vc_col-xs-12 " id="id_content_<?= $index; ?>_div">
			<h3>
				<label for="content_<?= $index; ?>"><?= __("Content") . " $p" ?></label>
			</h3>
			<div class="inside">
				<textarea id="content_<?= $index; ?>" name="content[<?= $index; ?>]" class="form-control "><?= $value ?></textarea>
				<p></p>
			</div>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	function clearTC() {
		bii_page_perso_content::deleteWhere("id_page_perso = $this->id");
		bii_page_perso_timeline::deleteWhere("id_page_perso = $this->id");
		bii_page_perso_image::deleteWhere("id_page_perso = $this->id");
	}

	function insertTC($post) {
		$this->clearTC();
		$array_insert["id_page_perso"] = $this->id;
		if (isset($post["content"])) {
			foreach ($post["content"] as $content) {
				$itemcontent = new bii_page_perso_content();
				$itemcontent->insert();
				$array_insert["contenu"] = $content;
				$itemcontent->updateChamps($array_insert);
			}
		}
		if (isset($post["timeline"])) {
			$index = 0;
			foreach ($post["timeline"] as $content) {
				if ($index != 0) {
					//Le timeline 0 correspond au placeholder
					$itemcontent = new bii_page_perso_timeline();
					$itemcontent->insert();
					$array_insert["contenu"] = $content;
					$date = $post["date_timeline"][$index];
					$options = $post["option_timeline"][$index];
					$array_insert["contenu"] = $content;
					$array_insert["date"] = $date;
					$array_insert["options"] = $options;
					$itemcontent->updateChamps($array_insert);
				}
				++$index;
			}
		}
		if (isset($post["id_logo"])) {
			$imagelogo = new bii_page_perso_image();
			$imagelogo->insert();
			$array_insert["post_id_sattel"] = $post["id_logo"];
			$array_insert["image_type"] = "logo";

			$imagelogo->updateChamps($array_insert);
		}
		if (isset($post["id_caroussel"])) {
			$values = explode(",", $post["id_caroussel"]);
			foreach ($values as $value) {
				$image = new bii_page_perso_image();
				$image->insert();
				$array_insert["post_id_sattel"] = $value;
				$array_insert["image_type"] = "caroussel";

				$image->updateChamps($array_insert);
			}
		}
	}

	function layout() {
		return new bii_page_perso_layout($this->id_layout);
	}

	function trad_layoutshortcodeBuild($layout) {
		$countppblock = substr_count($layout, "BII_PP_BLOCK_");
		$layout = str_replace("[BII_PP_TITLE]", "<h2>" . $this->titre() . "</h2>", $layout);
		$layout = str_replace("[BII_PP_LOGO]", $this->makelogoFront(), $layout);
		$layout = str_replace("[BII_PP_CAROUSSEL]", $this->makeCarousselFront(), $layout);
		$layout = str_replace("[BII_PP_TIMELINE]", $this->makeTimelineFront(), $layout);
		if ($countppblock) {
			for ($i = 0; $i <= $countppblock; ++$i) {
//				pre("[BII_PP_BLOCK_$i]");
				$layout = str_replace("[BII_PP_BLOCK_$i]", $this->makeContentFront($i), $layout);
			}
		}
		return $layout;
	}

	function makelogoFront($size = "full") {
		$id_logo = 0;
		$images = $this->getImage();
		if (isset($images[0])) {
			$id_logo = $images[0]->post_id();

			$text = "[vc_single_image image='$id_logo' img_size='$size']";
		}
		return $text;
	}

	function makeCarousselFront($size = "full") {
		$images = $this->getImages();
		$text = "";
		if (isset($images[0])) {
			$ids_post = [];
			foreach ($images as $image) {
				$ids_post[] = $image->post_id();
			}
			$imagestr = implode(",", $ids_post);
			$text .= "[vc_gallery interval='3' images='$imagestr' img_size='$size']";
		}
		return $text;
	}

	function makeTimelineFront() {
		$contents_array = $this->getTimeline();
		$text = "";
		foreach ($contents_array as $timeline) {
			$text .= $timeline->timeline_front();
		}
		return $text;
	}

	function makeContentFront($index = 0) {
		$contents_array = $this->getContents();
		$value = "";
		if (isset($contents_array[$index])) {
			$value = utf8_encode($contents_array[$index]->contentFront());
		}
		return $value;
	}

	function buildpost($is_satell = true) {
		ini_set('display_errors', '1');
		$layout = $this->layout()->layout();
//		pre($layout);
		$layout = $this->trad_layoutshortcodeBuild($layout);
//		echo do_shortcode($layout);
		$postarr = [];
		if ($this->post_id()) {
			$post_id = $this->post_id();
		} else {
			$post_id = 0;
		}
		$postarr['post_type'] = "page-pro";
		$postarr['post_title'] = $this->titre();
		$postarr['post_status'] = "publish";
		$postarr["ID"] = $post_id;
		$postarr['post_content'] = $layout;
		$post_id = wp_insert_post($postarr);
//		pre($post_id,"violet");
		if (!$this->post_id()) {
			$satell = "_satell";
			if (!$is_satell) {
				$satell = "_main";
			}
			$lang = "en";
			if ($this->lang == "en") {
				$lang = "fr";
			}
//			pre("updateChamps post_id$satell", "violet");
			$arr = ["post_id$satell" => $post_id];
			$this->updateChamps($arr);
//			pre($this);
			$trad = $this->getTradIdPost($lang);
			if ($trad != -1) {
				$icls = icl_translations::all_id("element_id = '$trad'");
				$trid = 0;
				foreach ($icls as $iclid) {
					$iclitem = new icl_translations($iclid);
//					pre($iclitem,"violet");
					$trid = $iclitem->trid();
				}
				$icls = icl_translations::all_id("element_id = '$post_id'");
				foreach ($icls as $iclid) {
					$niclitem = new icl_translations($iclid);
					$array = ["trid" => $trid, "source_language_code" => $lang];
//					pre($array,"green");
					$niclitem->updateChamps($array);
//					pre($niclitem, "purple");
				}
			}
		}
	}

}
