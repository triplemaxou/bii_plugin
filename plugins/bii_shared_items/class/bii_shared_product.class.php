<?php

class bii_shared_product extends bii_shared_item {

	protected $id;
	protected $id_parent;
	protected $id_trad;
	protected $id_posts;
	protected $lang;
	protected $id_bii_instance;
	protected $link;
	protected $is_lang_checked;

	function option_value() {

		return utf8_encode($this->id_posts);
	}

	function getInstance() {
		return new bii_instance($this->id_bii_instance);
	}

	function get_post() {
		if ($this->id_bii_instance == bii_instance::get_my_id()) {
			return get_post($this->id_posts);
		}
		return null;
	}

	function id_trad() {
		$id_trad = $this->id_trad;
		if (!$id_trad) {
			
		}
		return $id_trad;
	}

	function source_language_code() {
		$slc = null;
		if ($this->id_trad()) {
			$slc = "en";
		}
		return $slc;
	}

	function changelang() {
		if (!$this->is_lang_checked) {

			$args = [
				'element_id' => $this->id_posts(),
				'element_type' => "product",
				'trid' => $this->id_trad(),
				'language_code' => $this->lang(),
				'source_language_code' => $this->source_language_code()
			];
			do_action('wpml_set_element_language_details', $args);
			$this->updateChamps(1, "is_lang_checked");
		}
	}

	static function posts_from_instance($id_instance, $return = "id_posts") {
		if (is_a($id_instance, "bii_instance")) {
			$id_instance = $id_instance->id();
		}
		$req = "id_bii_instance = '$id_instance'";
		$liste = static::all_items($req);
		if ($return == "shared_product") {
			return $liste;
		}
		$liste_return = [];
		foreach ($liste as $item) {
			if ($return == "id_posts") {
				$liste_return[] = $item->id_posts();
			}
			if ($return == "id_shared") {
				$liste_return[] = $item->id();
			}
			if ($return == "posts") {
				$liste_return[] = $item->get_post();
			}
		}
		return $liste_return;
	}

	static function checklangs() {
		$myinstance = bii_instance::get_me();
		$instanceid = $myinstance->id();
		$liste_shared_product = static::posts_from_instance($instanceid, "shared_product");
		foreach ($liste_shared_product as $item) {
			$item->changelang();
		}
	}

	static function add_post($id_posts, $id_instance, $lang, $id_parent = 0, $id_trad = 0) {
		if (is_a($id_instance, "bii_instance")) {
			$id_instance = $id_instance->id();
		}


		$sp = new static();
		$sp->insert();
		$value = [
			"id_posts" => $id_posts,
			"id_bii_instance" => $id_instance,
			"lang" => $lang,
			"id_parent" => $id_parent,
			"id_trad" => $id_trad,
		];
		$sp->updateChamps($value);
		return $sp->id();
	}

	static function update_shared_product($id_posts, $id_bii_instance, $lang, $id_parent = 0, $id_trad = 0) {
		if (is_a($id_bii_instance, "bii_instance")) {
			$id_bii_instance = $id_bii_instance->id();
		}
		$req = "id_posts = '$id_posts' AND lang = '$lang' AND id_bii_instance = $id_bii_instance";
		$nb = static::nb($req);
		if ($nb) {
			$id = static::all_id($req)[0];
		} else {
			$id = static::add_post($id_posts, $id_bii_instance, $lang, $id_parent, $id_trad);
		}
		return $id;
	}

	function bodypost($instance) {
		//get post object
		if (is_int($instance)) {
			$instance = new bii_instance($instance);
		}
		$url = $instance->url_import();
		$pass = $instance->password_import();
		$post = new posts($this->id_posts);

		return $post->wp_remote_postbody($url, $pass, $this->lang());
	}

	function send_request($instance) {
		$body = $this->bodypost($instance);
		$url = $instance->url_import();
		$url .= "&icl_post_language=" . $this->lang();
		return wp_remote_post($url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $body,
			'cookies' => array()
		));
	}

	function postToInstance($otherinstance) {
		if (is_int($otherinstance)) {
			$otherinstance = new bii_instance($otherinstance);
		}
		$response = $this->send_request($otherinstance);
//		pre($response, "green");
		$remote_post_ids = get_post_meta($this->id_posts, 'remote_post_id', true);
		$output = json_decode($response['body'], true);
		$remote_post_id = $output['remote_post_id'];
		if (!is_array($remote_post_ids)) {
			$remote_post_ids = array();
		}
		$remote_post_ids[$otherinstance->url_import()] = $remote_post_id;
		update_post_meta($this->id_posts, 'remote_post_id', $remote_post_ids);

		return static::update_shared_product($remote_post_id, $otherinstance, $this->lang, $this->id, 0);
	}

}
