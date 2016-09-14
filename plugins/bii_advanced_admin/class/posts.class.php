<?php

class posts extends global_class {

	protected $ID;
	protected $post_author;
	protected $post_date;
	protected $post_date_gmt;
	protected $post_content;
	protected $post_title;
	protected $post_excerpt;
	protected $post_status;
	protected $comment_status;
	protected $ping_status;
	protected $post_password;
	protected $post_name;
	protected $to_ping;
	protected $pinged;
	protected $post_modified;
	protected $post_modified_gmt;
	protected $post_content_filtered;
	protected $post_parent;
	protected $guid;
	protected $menu_order;
	protected $post_type;
	protected $post_mime_type;
	protected $comment_count;

	public static function identifiant() {
		return "ID";
	}

	public static function insertDefault() {
		$bloginfo = get_bloginfo("url");

		$item = new static();
		$item->insert();
		$values = [
			"post_author" => 1,
			"post_status" => "publish",
			"comment_status" => "closed",
			"ping_status" => "closed",
			"post_parent" => 0,
			"guid" => $bloginfo . "/?post_type=listing&#038;p=" . $item->id(),
			"menu_order" => 0,
			"post_type" => "listing",
			"comment_count" => 0,
		];
//		$item->updateChamps($values);
		var_dump($values);
		return $item->id();
	}

	public function unpublish($verbose = false) {
		$id = $this->id();
		$my_post = array(
			'ID' => $id,
			'post_status' => 'trash',
		);
		wp_update_post($my_post);
		if ($verbose) {
			$id = $this->id();
			echo " <br /> $id annonce dépubliée";
		}
	}

	public function publish($verbose = false) {
		$id = $this->id();
		wp_publish_post($id);
		if ($verbose) {
			$id = $this->id();
			echo " <br /> $id annonce dépubliée";
		}
	}

	public static function fromGuid($guid, $type = "objet") {
		$id = 0;
		$where = "guid = '$guid'";
		$list = static::all_id($where);
		foreach ($list as $iid) {
			$id = $iid;
		}
		if ("id" == $type) {
			return $id;
		}
		if ("objet" == $type) {
			return new static($id);
		}
	}

	public static function currentUserPosts($post_type = "ignition_product") {
		$user_id = get_current_user_id();
		if ($user_id) {
			$where = "post_author = $user_id AND post_status NOT IN ('trash')";
			if ($post_type) {
				$where .= " AND post_type = '$post_type'";
			}
			return static::all_id($where);
		} else {
			$where = "post_status NOT IN ('trash')";
			if ($post_type) {
				$where .= " AND post_type = '$post_type'";
			}
			return static::all_id($where);
		}
	}

	public function getCategories($where = "") {
		$prefix = static::prefix_bdd();
		$term_relationships = $prefix . "term_relationships";
		$wheredefault = "term_taxonomy_id in(select term_taxonomy_id from $term_relationships where object_id = " . $this->id() . ")";
		$liste_ids = term_taxonomy::get_term_ids($wheredefault . $where);
		$cats = [];
		foreach ($liste_ids as $id) {
			$cats[] = new terms($id);
		}
		return $cats;
	}

	public static function getpostwithmeta($meta, $value) {
		if (is_array($value)) {
			$value = implode("','", $value);
		}
		$req = "ID in (select post_id from wp_cfwww_postmeta where meta_key = '$meta' and meta_value in ('$value'))";
		return static::all_id($req);
	}
	
	public static function posts_not_in_lang(){
		$lang = bii_multilingual_current_language();
		$req2 = "ID IN (select distinct element_id from wp_cfwww_icl_translations where language_code NOT IN ('$lang')) and post_author = 1 and post_type = 'post' and post_status = 'publish'";
		$arrnotlisteid = static::all_id($req2);
		$arrnot = static::getpostwithmeta("_related_id", $arrnotlisteid);	
		return array_unique($arrnot);
	}

	public static function posts_by_biilink_agency_in_good_language_and_followed($user_id = null) {
		$lang = bii_multilingual_current_language();
		if ($user_id == null) {
			$user_id = get_current_user_id();
		}
		$arr1 = $arr2 = [];
		$in_user_id = implode("','", apply_filters('bii_get_followers_id', $user_id));
		$in_user_id .= "','" . $user_id;
//		pre($in_user_id,'green');
		$req = "ID IN (select distinct element_id from wp_cfwww_icl_translations where language_code = '$lang') and post_author = 1 and post_type = 'post' and post_status = 'publish'";
		$req2 = "ID IN (select distinct element_id from wp_cfwww_icl_translations where language_code NOT IN ('$lang')) and post_author = 1 and post_type = 'post' and post_status = 'publish'";
		$arrlisteid = static::all_id($req);
		$arrnotlisteid = static::all_id($req2);
		$arr1 = static::getpostwithmeta("_related_id", $arrlisteid);
		$arr2 = static::getpostwithmeta('_user_id',$in_user_id);
		
		$arrnot = static::getpostwithmeta("_related_id", $arrnotlisteid);	
		$arrtype = static::all_id("post_type = 'um_activity'");
		
		$merge = array_merge($arr1,$arr2);
		$merge = array_unique($merge);
//		pre($merge,"orange");
//		pre($arrnot,"red");

		$diff = array_diff($merge,$arrnot);
		
		$intersec = array_intersect($diff,$arrtype);
		
//		pre($diff,"orange");
//		pre($arrtype,"purple");
//		pre($intersec,"green");
		return $intersec;
	}
	
	function wp_remote_postbody($url,$pass,$lang = null) {
		//get post object		
		$post_id = $this->ID;
		$post = get_post($post_id, ARRAY_A);

		//get post meta
		$post_meta = get_post_meta($post_id);
		if($lang == null){
			$lang = apply_filters("bii_multilingual_current_language",$lang);
			
		}
		
		unset($post_meta['remote_post_id']);

		$remote_post_ids = get_post_meta($post_id, 'remote_post_id', true);
		if ($remote_post_ids) {
			if (!empty($remote_post_ids[$url])) {
				$post_meta['remote_post_id'][0] = $remote_post_ids[$url];
			}
		}
		//get custom taxonmies registered against this post type
		$post_type = $post['post_type'];
		$taxonomies = get_object_taxonomies($post_type, 'objects');
		$post_taxonomies_cats = array();
		$post_taxonomies_tags = array();
		foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
			//save post terms in the respective taxonomy index
			$terms = wp_get_post_terms($post_id, $taxonomy_slug);
			if (!is_wp_error($terms)) {
				$termstosync = array();
				foreach ($terms as $key => $term) {
					$t_id = $term->term_id;
					$option_name = $taxonomy_slug . "_" . $t_id;
					if (!empty($t_id)) {
						if (get_option($option_name) != '-1') {
							$termstosync[] = $term;
						} else {
							return -1;
						}
					}
				}
				if ($taxonomy->hierarchical == 1) {
					$post_taxonomies_cats[$taxonomy_slug] = $termstosync;
				} else {
					$post_taxonomies_tags[$taxonomy_slug] = $termstosync;
				}
			}
		}
		//check if post has featured image
		$featuredimage_url = '';
		if (has_post_thumbnail($post_id)) {
			$post_thumbnail_id = get_post_thumbnail_id($post_id);
			$featuredimage_url = wp_get_attachment_url($post_thumbnail_id);
		}

		//check if post type is woocommerce product
		if ($post['post_type'] == 'product') {
			$galleryimages = explode(',', $post_meta['_product_image_gallery'][0]);
			$_product_image_gallery_urls = array();
			if ($galleryimages) {
				foreach ($galleryimages as $key => $image) {
					$_product_image_gallery_urls[$image] = wp_get_attachment_url($image);
				}
			}
			$body['product_image_gallery_urls'] = $_product_image_gallery_urls;
		}


		//remove the things we don't need
		unset($post['guid']);
		unset($post_meta['_edit_lock']);
		unset($post_meta['_edit_last']);
		unset($post_meta['_pingme']);
		unset($post_meta['_encloseme']);
		unset($post_meta['_thumbnail_id']);


		$body['passkey'] = $pass;
		$body['post'] = $post;
		$body['post_type'] = $post_type;
		$body['post_meta'] = $post_meta;
		$body['featuredimage_url'] = $featuredimage_url;
		$body['post_taxonomies_cats'] = $post_taxonomies_cats;
		$body['post_taxonomies_tags'] = $post_taxonomies_tags;
		$body["_wpml_language"] = $lang;
		$post_meta["_wpml_language"] = $lang;
		return $body;
	}

}
