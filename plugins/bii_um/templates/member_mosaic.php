<?php
$bii_users = users::all_id();

$count = 0;
$userswithphoto = [];

$default = '';
$alt = '';

foreach ($bii_users as $bii_user_id) {
	$img = get_avatar($bii_user_id, $size, $default, $alt);
	if (strpos($img, "default_avatar.jpg") === false && $count < $limit) {
		$userswithphoto[] = $bii_user_id;
	}
}
shuffle($userswithphoto);
//	pre($userswithphoto);
$countusers = count($userswithphoto);
$premierpassage = true;
$clearline = $limit / $numberline;
$height = $size * $numberline;

$makeid = rand();
?>

<div id="bii-member-mosaic-<?=$makeid; ?>" class="bii-member-mosaic <?= $class_add ?>">
	<?php
	for ($i = 1; $i <= $limit; ++$i) {
		
		$index = $i % $countusers;
		--$index;
		if ($index == -1) {
			$index = $countusers - 1;

			shuffle($userswithphoto);
		}
		$bii_user_id = $userswithphoto[$index];
//		pre("userswithphoto[$index]");
//		pre($bii_user_id);

		$alt = "";

		$bii_user = new users($bii_user_id);
		um_fetch_user($bii_user_id);
		$name = $bii_user->display_name();
		$alt = __("Photo de $name");
		$img = get_avatar($bii_user_id, $size, $default, $alt);
		?>
		<div class="bii-mosaic-item bii-member-tile bii-member-tile-<?= $bii_user_id; ?> <?= $class_add_tile ?> number-tile-<?= $i; ?>">
			<?php if ($displaylink) { ?>
				<a href="<?= apply_filters("bii_multilingual_filter_um_localize_permalink", um_user_profile_url()) ; ?>">
					<?php
				}
				echo apply_filters('bii_um_get_avatar', $img, $bii_user_id, $size, $default, $alt, $lazyload);
				if ($displaylink) {
					?>
				</a>
			<?php } ?>
		</div>
		<?php
		if ($i % $clearline == 0) {
			?>
			<div class="clear clearfix"></div>
			<?php
		}

		++$count;
		if ($content && $count == $display_content_after_tile) {
			?>
			<div class="bii-mosaic-item bii-member-mosaic-content <?= $class_add_tile_content ?>">
				<?= $content; ?>
			</div>
			<?php
		}
		$premierpassage = false;
		um_reset_user_clean();
	}
	
	um_reset_user();
	?>
</div>
<?php
