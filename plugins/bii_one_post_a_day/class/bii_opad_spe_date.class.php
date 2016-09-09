<?php

class bii_opad_spe_date extends bii_item_opad {

	protected $id;
	protected $name;
	protected $day;
	protected $month;
	protected $year;
	protected $lang;
	protected $id_posts;

	static function get_post_of_the_day() {
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$lang = apply_filters("bii_multilingual_current_language", '');
		$req = "day = '$day' AND month = '$month' AND year = '$year' AND lang ='$lang'";
		$id_posts = 0;
		if (static::nb($req)) {
			$items = static::all_items($req);
			if (isset($items[0])) {
				$id_posts = $items[0]->id_posts();
			}
		}

		if ($id_posts == 0) {

			$date = date("z");
			$count_opad = bii_posts_opad::nb();
			$id = $date % $count_opad + 1;
			$item = new bii_posts_opad($id);
			pre($item,'green');
			$id_posts = $item->id_posts();
			
		}
		return $id_posts;
	}

}
