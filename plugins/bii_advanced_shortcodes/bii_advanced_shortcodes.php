<?php
/*
  Plugin Name: Bii advanced shortcodes
  Description: Ajoute des shortcodes avancés
  Version: 1.3
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_advanced_shortcodes', '1.3');

function bii_SC_displaywhenrequest($atts, $content = null) {
	$display = true;
	foreach ($atts as $attr => $value) {
		$display = false;
		if (isset($_REQUEST[$attr]) && ($_REQUEST[$attr] == $value || $value == "all")) {
			$display = true;
		}
	}
	$return = "";
	if ($display) {
		$return = do_shortcode($content);
	}
	return $return;
}

function bii_SC_notdisplaywhenrequest($atts, $content = null) {
	foreach ($atts as $attr => $value) {
		$display = true;
		if (isset($_REQUEST[$attr]) && ($_REQUEST[$attr] == $value || $value == "all")) {
			$display = false;
		}
	}
	$return = "";
	if ($display) {
		$return = do_shortcode($content);
	}
	return $return;
}

function bii_loremipsum($atts, $content = null) {
	if (!isset($atts["lines"])) {
		$atts["lines"] = 10;
	}
	$lines = $atts["lines"];
	$content = file_get_contents("http://loripsum.net/api/$lines/decorate/link/ul");
	return $content;
}

function bii_SC_image_une($atts) {
	$id = null;
	$size = "full";
	if (isset($atts["id"])) {
		$id = $atts["id"];
	}
	if (isset($atts["size"])) {
		$size = $atts["size"];
	}

	return get_the_post_thumbnail($id, $size);
}

function bii_SC_image_une_src($atts) {
	$id = null;
	if (isset($atts["id"])) {
		$id = $atts["id"];
	}
	return wp_get_attachment_thumb_url($id);
}

function bii_SC_tower_titles($atts = [], $content = '') {
	$contents = "";
	if ($atts["titres"]) {
		ob_start();
		?>
		<div class="bii_tower_titles vc_col-xs-5 vc_col-sm-4 vc_col-md-3">
			<?php
			$explode = explode(",", $atts["titres"]);
			foreach ($explode as $titre) {
				?>
				<div class="bii_tower_title <?= strtolower(stripAccents($titre)); ?>" data-affiche="<?= strtolower(stripAccents($titre)); ?>">
					<a href="#"><?= $titre; ?></a>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}

function bii_SC_tower_items($atts = [], $content = '') {
	$contents = "";
	ob_start();
	?>
	<div class="bii_tower_items vc_col-xs-7 vc_col-sm-8 vc_col-md-9">
		<?= do_shortcode($content); ?>
	</div> 
	<?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

function bii_SC_tower_item($atts = [], $content = '') {
	$contents = "";
	if ($atts["titre"]) {
		ob_start();
		?>
		<div class="bii_tower_item <?= strtolower(stripAccents($atts["titre"])); ?> hidden">
			<?= do_shortcode($content); ?>
		</div> 
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
	}
	return $contents;
}

function bii_SC_get_bloginfo($atts = [], $content = '') {
	$contents = "";
	if ($atts["info"]) {
		$contents = get_bloginfo($atts["info"]);
	}
	return $contents;
}

function bii_SC_just_icon_shortcode($atts = []) {
	$icon_type = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $icon_link = $el_class = $icon_animation = $tooltip_disp = $tooltip_text = $icon_align = '';
	extract(shortcode_atts(array(
		'icon_type' => 'selector',
		'icon' => 'none',
		'icon_img' => '',
		'img_width' => '48',
		'icon_size' => '32',
		'icon_color' => '#333',
		'icon_style' => 'none',
		'icon_color_bg' => '#ffffff',
		'icon_color_border' => '#333333',
		'icon_border_style' => '',
		'icon_border_size' => '1',
		'icon_border_radius' => '500',
		'icon_border_spacing' => '50',
		'icon_link' => '',
		'icon_animation' => 'none',
		'tooltip_disp' => '',
		'tooltip_text' => '',
		'el_class' => '',
		'icon_align' => 'center',
		'css_just_icon' => '',
			), $atts));
	$is_preset = false;
	if (isset($_GET['preset'])) {
		$is_preset = true;
	}
	$css_just_icon = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css_just_icon, ' '), "just_icon", $atts);
	$css_just_icon = esc_attr($css_just_icon);
	$ultimate_js = get_option('ultimate_js');
	if ($tooltip_text != '' && $ultimate_js == 'disable')
		wp_enqueue_script('ultimate-tooltip');

	$output = $style = $link_sufix = $link_prefix = $target = $href = $icon_align_style = $css_trans = '';

	if (trim($icon_animation) === '')
		$icon_animation = 'none';

	if ($icon_animation !== 'none') {
		$css_trans = 'data-animation="' . $icon_animation . '" data-animation-delay="03"';
	}

	$uniqid = uniqid();
	if ($icon_link !== '') {
		$href = vc_build_link($icon_link);
		$target = (isset($href['target'])) ? "target='" . $href['target'] . "'" : '';
		$link_prefix .= '<a class="aio-tooltip ' . $uniqid . '" href = "' . $href['url'] . '" ' . $target . ' data-toggle="tooltip" data-placement="' . $tooltip_disp . '" title="' . $tooltip_text . '">';
		$link_sufix .= '</a>';
	} else {
		if ($tooltip_disp !== "") {
			$link_prefix .= '<div class="aio-tooltip ' . $uniqid . '" href = "' . $href . '" ' . $target . ' data-toggle="tooltip" data-placement="' . $tooltip_disp . '" title="' . $tooltip_text . '">';
			$link_sufix .= '</div>';
		}
	}

	$elx_class = '';

	/* position fix */
	if ($icon_align == 'right')
		$icon_align_style .= 'text-align:right;';
	elseif ($icon_align == 'center')
		$icon_align_style .= 'text-align:center;';
	elseif ($icon_align == 'left')
		$icon_align_style .= 'text-align:left;';

	if ($icon_type == 'custom') {

		$img = apply_filters('ult_get_img_single', $icon_img, 'url');
		$alt = apply_filters('ult_get_img_single', $icon_img, 'alt');
		//$title = apply_filters('ult_get_img_single', $icon_img, 'title');
		//$description = apply_filters('ult_get_img_single', $icon_img, 'description');
		//$caption = apply_filters('ult_get_img_single', $icon_img, 'caption');

		if ($icon_style !== 'none') {
			if ($icon_color_bg !== '')
				$style .= 'background:' . $icon_color_bg . ';';
		}
		if ($icon_style == 'circle') {
			$elx_class.= ' uavc-circle ';
		}
		if ($icon_style == 'square') {
			$elx_class.= ' uavc-square ';
		}
		if ($icon_style == 'advanced' && $icon_border_style !== '') {
			$style .= 'border-style:' . $icon_border_style . ';';
			$style .= 'border-color:' . $icon_color_border . ';';
			$style .= 'border-width:' . $icon_border_size . 'px;';
			$style .= 'padding:' . $icon_border_spacing . 'px;';
			$style .= 'border-radius:' . $icon_border_radius . 'px;';
		}

		if (!empty($img)) {
			if ($icon_link == '' || $icon_align == 'center') {
				$style .= 'display:inline-block;';
			}
			$output .= "\n" . $link_prefix . '<div class="aio-icon-img ' . $elx_class . '" style="font-size:' . $img_width . 'px;' . $style . '" ' . $css_trans . '>';
			$output .= "\n\t" . '<img class="img-icon lazyquick" alt="' . $alt . '" data-original="' . $img . '"  />';
			$output .= "\n" . '</div>' . $link_sufix;
		}
		$output = $output;
	} else {
		if ($icon_color !== '')
			$style .= 'color:' . $icon_color . ';';
		if ($icon_style !== 'none') {
			if ($icon_color_bg !== '')
				$style .= 'background:' . $icon_color_bg . ';';
		}
		if ($icon_style == 'advanced') {
			$style .= 'border-style:' . $icon_border_style . ';';
			$style .= 'border-color:' . $icon_color_border . ';';
			$style .= 'border-width:' . $icon_border_size . 'px;';
			$style .= 'width:' . $icon_border_spacing . 'px;';
			$style .= 'height:' . $icon_border_spacing . 'px;';
			$style .= 'line-height:' . $icon_border_spacing . 'px;';
			$style .= 'border-radius:' . $icon_border_radius . 'px;';
		}
		if ($icon_size !== '')
			$style .='font-size:' . $icon_size . 'px;';
		if ($icon_align !== 'left') {
			$style .= 'display:inline-block;';
		}
		if ($icon !== "") {
			$output .= "\n" . $link_prefix . '<div class="aio-icon ' . $icon_style . ' ' . $elx_class . '" ' . $css_trans . ' style="' . $style . '">';
			$output .= "\n\t" . '<i class="' . $icon . '"></i>';
			$output .= "\n" . '</div>' . $link_sufix;
		}
		$output = $output;
	}
	if ($tooltip_disp !== "") {
		$output .= '<script>
					jQuery(function () {
						jQuery(".' . $uniqid . '").bsf_tooltip("hide");
					})
				</script>';
	}
	/* alignment fix */
	if ($icon_align_style !== '') {
		$output = '<div class="align-icon" style="' . $icon_align_style . '">' . $output . '</div>';
	}

	$output = '<div class="ult-just-icon-wrapper ' . $el_class . ' ' . $css_just_icon . '">' . $output . '</div>';

	if ($is_preset) {
		$text = 'array ( ';
		foreach ($atts as $key => $att) {
			$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
		}
		if ($content != '') {
			$text .= '<br/>	\'content\' => \'' . $content . '\',';
		}
		$text .= '<br/>)';
		$output .= '<pre>';
		$output .= $text;
		$output .= '</pre>';
	}

	return $output;
}

function bii_SC_icon_boxes($atts, $content = null) {
	$icon_type = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $el_class = $icon_animation = $title = $link = $hover_effect = $pos = $read_more = $read_text = $box_border_style = $box_border_width = $box_border_color = $box_bg_color = $pos = $css_class = $desc_font_line_height = $title_font_line_height = '';
	$title_font = $title_font_style = $title_font_size = $title_font_color = $desc_font = $desc_font_style = $desc_font_size = $desc_font_color = $box_min_height = '';
	extract(shortcode_atts(array(
		'icon_type' => 'selector',
		'icon' => 'none',
		'icon_img' => '',
		'img_width' => '48',
		'icon_size' => '32',
		'icon_color' => '#333',
		'icon_style' => 'none',
		'icon_color_bg' => '#ffffff',
		'icon_color_border' => '#333333',
		'icon_border_style' => '',
		'icon_border_size' => '1',
		'icon_border_radius' => '500',
		'icon_border_spacing' => '50',
		'icon_animation' => '',
		'title' => '',
		'link' => '',
		'hover_effect' => 'style_1',
		'pos' => 'default',
		'box_min_height' => '',
		'box_border_style' => '',
		'box_border_width' => '',
		'box_border_color' => '',
		'box_bg_color' => "",
		'read_more' => 'none',
		'read_text' => 'Read More',
		'title_font' => '',
		'title_font_style' => '',
		'title_font_size' => '',
		'title_font_line_height' => '',
		'title_font_color' => '',
		'desc_font' => '',
		'desc_font_style' => '',
		'desc_font_size' => '',
		'desc_font_color' => '',
		'desc_font_line_height' => '',
		'el_class' => '',
		'css_info_box' => '',
			), $atts, 'bsf-info-box'));
	$html = $target = $suffix = $prefix = $title_style = $desc_style = $inf_design_style = '';
	$inf_design_style = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css_info_box, ' '), "bsf-info-box", $atts);
	$inf_design_style = esc_attr($inf_design_style);
	//$font_args = array();
	//echo $pos; die();
	$box_icon = do_shortcode('[just_icon icon_type="' . $icon_type . '" icon="' . $icon . '" icon_img="' . $icon_img . '" img_width="' . $img_width . '" icon_size="' . $icon_size . '" icon_color="' . $icon_color . '" icon_style="' . $icon_style . '" icon_color_bg="' . $icon_color_bg . '" icon_color_border="' . $icon_color_border . '"  icon_border_style="' . $icon_border_style . '" icon_border_size="' . $icon_border_size . '" icon_border_radius="' . $icon_border_radius . '" icon_border_spacing="' . $icon_border_spacing . '" icon_animation="' . $icon_animation . '"]');
	$prefix .= '<div class="aio-icon-component ' . $inf_design_style . ' ' . $css_class . ' ' . $el_class . ' ' . $hover_effect . '">';
	$suffix .= '</div> <!-- aio-icon-component -->';
	$ex_class = $ic_class = '';
	if ($pos != '') {
		$ex_class .= $pos . '-icon';
		$ic_class = 'aio-icon-' . $pos;
	}

	/* title */
	if ($title_font != '') {
		$font_family = get_ultimate_font_family($title_font);
		if ($font_family != '')
			$title_style .= 'font-family:\'' . $font_family . '\';';
		//array_push($font_args, $title_font);
	}
	if ($title_font_style != '')
		$title_style .= get_ultimate_font_style($title_font_style);
	// if($title_font_size != '')
	// 	$title_style .= 'font-size:'.$title_font_size.'px;';
	// if($title_font_line_height != '')
	// 	$title_style .= 'line-height:'.$title_font_line_height.'px;';

	if (is_numeric($title_font_size)) {
		$title_font_size = 'desktop:' . $title_font_size . 'px;';
	}
	if (is_numeric($title_font_line_height)) {
		$title_font_line_height = 'desktop:' . $title_font_line_height . 'px;';
	}
	$info_box_id = 'Info-box-wrap-' . rand(1000, 9999);
	$info_box_args = array(
		'target' => '#' . $info_box_id . ' .aio-icon-title', // set targeted element e.g. unique class/id etc.
		'media_sizes' => array(
			'font-size' => $title_font_size, // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
			'line-height' => $title_font_line_height
		),
	);
	$info_box_data_list = get_ultimate_vc_responsive_media_css($info_box_args);

	if ($title_font_color != '')
		$title_style .= 'color:' . $title_font_color . ';';

	/* description */
	if ($desc_font != '') {
		$font_family = get_ultimate_font_family($desc_font);
		if ($font_family !== '')
			$desc_style .= 'font-family:\'' . $font_family . '\';';
		//array_push($font_args, $desc_font);
	}
	if ($desc_font_style != '')
		$desc_style .= get_ultimate_font_style($desc_font_style);
	// if($desc_font_size != '')
	// 	$desc_style .= 'font-size:'.$desc_font_size.'px;';
	// if($desc_font_line_height != '')
	// 	$desc_style .= 'line-height:'.$desc_font_line_height.'px;';

	if (is_numeric($desc_font_size)) {
		$desc_font_size = 'desktop:' . $desc_font_size . 'px;';
	}
	if (is_numeric($desc_font_line_height)) {
		$desc_font_line_height = 'desktop:' . $desc_font_line_height . 'px;';
	}

	$info_box_desc_args = array(
		'target' => '#' . $info_box_id . ' .aio-icon-description', // set targeted element e.g. unique class/id etc.
		'media_sizes' => array(
			'font-size' => $desc_font_size, // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
			'line-height' => $desc_font_line_height
		),
	);
	$info_box_desc_data_list = get_ultimate_vc_responsive_media_css($info_box_desc_args);
	if ($desc_font_color != '')
		$desc_style .= 'color:' . $desc_font_color . ';';
	//enquque_ultimate_google_fonts($font_args);

	$box_style = $box_style_data = '';
	if ($pos == 'square_box') {
		if ($box_min_height != '') {
			$box_style_data .="data-min-height='" . $box_min_height . "px'";
		}
		if ($box_border_color != '') {
			$box_style .="border-color:" . $box_border_color . ";";
		}
		if ($box_border_style != '') {
			$box_style .="border-style:" . $box_border_style . ";";
		}
		if ($box_border_width != '') {
			$box_style .="border-width:" . $box_border_width . "px;";
		}
		if ($box_bg_color != '') {
			$box_style .="background-color:" . $box_bg_color . ";";
		}
	}
	$html .= '<div id="' . $info_box_id . '" class="aio-icon-box ' . $ex_class . '" style="' . $box_style . '" ' . $box_style_data . ' >';

	if ($pos == "heading-right" || $pos == "right") {
		if ($pos == "right") {
			$html .= '<div class="aio-ibd-block" >';
		}
		if ($title !== '') {
			$html .= '<div class="aio-icon-header" >';
			$link_prefix = $link_sufix = '';
			if ($link !== 'none') {
				if ($read_more == 'title') {
					$href = vc_build_link($link);
					if (isset($href['target']) && trim($href['target']) !== '') {
						$target = 'target="' . $href['target'] . '"';
					}
					$link_prefix = '<a class="aio-icon-box-link" href="' . $href['url'] . '" ' . $target . '>';
					$link_sufix = '</a>';
				}
			}
			$html .= $link_prefix . '<h3 class="aio-icon-title ult-responsive" ' . $info_box_data_list . ' style="' . $title_style . '">' . $title . '</h3>' . $link_sufix;
			$html .= '</div> <!-- header -->';
		}
		if ($pos !== "right") {
			if ($icon !== 'none' || $icon_img !== '')
				$html .= '<div class="' . $ic_class . '" >' . $box_icon . '</div>';
		}
		if ($content !== '') {
			$html .= '<div class="aio-icon-description ult-responsive" ' . $info_box_desc_data_list . ' style="' . $desc_style . '">';
			$html .= do_shortcode($content);
			if ($link !== 'none') {
				if ($read_more == 'more') {
					$href = vc_build_link($link);
					if (isset($href['target']) && $href['target'] != '') {
						$target = 'target="' . $href['target'] . '"';
					}
					$more_link = '<a class="aio-icon-read x" href="' . $href['url'] . '" ' . $target . '>';
					$more_link .= $read_text;
					$more_link .= '&nbsp;&raquo;';
					$more_link .= '</a>';
					$html .= $more_link;
				}
			}
			$html .= '</div> <!-- description -->';
		}
		if ($pos == "right") {
			$html .= '</div> <!-- aio-ibd-block -->';
			if ($icon !== 'none' || $icon_img !== '')
				$html .= '<div class="' . $ic_class . '">' . $box_icon . '</div>';
		}
	}
	else {
		//echo $icon_img; die();
		if ($icon !== 'none' || $icon_img != '')
			$html .= '<div class="' . $ic_class . '">' . $box_icon . '</div>';
		if ($pos == "left")
			$html .= '<div class="aio-ibd-block">';
		if ($title !== '') {
			$html .= '<div class="aio-icon-header" >';
			$link_prefix = $link_sufix = '';
			if ($link !== 'none') {
				if ($read_more == 'title') {
					$href = vc_build_link($link);
					if (isset($href['target']) && trim($href['target']) !== '') {
						$target = 'target="' . $href['target'] . '"';
					}
					$link_prefix = '<a class="aio-icon-box-link" href="' . $href['url'] . '" ' . $target . '>';
					$link_sufix = '</a>';
				}
			}
			$html .= $link_prefix . '<h3 class="aio-icon-title ult-responsive" ' . $info_box_data_list . ' style="' . $title_style . '">' . $title . '</h3>' . $link_sufix;
			$html .= '</div> <!-- header -->';
		}
		if ($content !== '') {
			$html .= '<div class="aio-icon-description ult-responsive" ' . $info_box_desc_data_list . ' style="' . $desc_style . '">';
			$html .= do_shortcode($content);
			if ($link !== 'none') {
				if ($read_more == 'more') {
					$href = vc_build_link($link);
					if (isset($href['target']) && trim($href['target']) != '') {
						$target = 'target="' . $href['target'] . '"';
					}
					$more_link = '<a class="aio-icon-read xx" href="' . $href['url'] . '" ' . $target . '>';
					$more_link .= $read_text;
					$more_link .= '&nbsp;&raquo;';
					$more_link .= '</a>';
					$html .= $more_link;
				}
			}
			$html .= '</div> <!-- description -->';
		}
		if ($pos == "left")
			$html .= '</div> <!-- aio-ibd-block -->';
	}


	$html .= '</div> <!-- aio-icon-box -->';
	if ($link !== 'none') {
		if ($read_more == 'box') {
			$href = vc_build_link($link);
			if (isset($href['target']) && trim($href['target']) !== '') {
				$target = 'target="' . $href['target'] . '"';
			}
			$output = $prefix . '<a class="aio-icon-box-link" href="' . $href['url'] . '" ' . $target . '>' . $html . '</a>' . $suffix;
		} else {
			$output = $prefix . $html . $suffix;
		}
	} else {
		$output = $prefix . $html . $suffix;
	}
	$is_preset = false; //Display settings for Preset
	if (isset($_GET['preset'])) {
		$is_preset = true;
	}
	if ($is_preset) {
		$text = 'array ( ';
		foreach ($atts as $key => $att) {
			$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
		}
		if ($content != '') {
			$text .= '<br/>	\'content\' => \'' . $content . '\',';
		}
		$text .= '<br/>)';
		$output .= '<pre>';
		$output .= $text;
		$output .= '</pre>';
	}
	return $output;
}


function bii_SC_displaytimestamp($attrs = [],$content=''){
	return time();
}
function bii_SC_displayindate($attrs = [],$content=''){
	$timestampbegin = 0;
	
	$contents = "";
	if(isset($attrs["begin"])){
		$timestampbegin = $attrs["begin"];
	}
	if(isset($attrs["end"])){
		$timestampend = $attrs["end"];
	}else{
		$timestampend = time() + 60000;
	}
	$now = time();
	if($now >= $timestampbegin && $now <= $timestampend){
		
//		$contents .= "$timestampbegin $timestampend <br />";
		$contents .= do_shortcode($content);
	}	
	return $contents;
}

add_shortcode('bii_displaywhenrequest', 'bii_SC_displaywhenrequest');
add_shortcode('bii_notdisplaywhenrequest', 'bii_SC_notdisplaywhenrequest');
add_shortcode('bii_loremipsum', 'bii_loremipsum');
add_shortcode('bii_imageune', 'bii_SC_image_une');
add_shortcode('bii_imageune_src', 'bii_SC_image_une_src');
add_shortcode('bii_tower_titles', 'bii_SC_tower_titles');
add_shortcode('bii_tower_items', 'bii_SC_tower_items');
add_shortcode('bii_tower_item', 'bii_SC_tower_item');
add_shortcode("bii_tmstp_match","bii_SC_displayindate");
add_shortcode("bii_current_tmstp","bii_SC_displaytimestamp");

add_shortcode('bii_getblog', 'bii_SC_get_bloginfo');

add_action('init', function() {
	remove_shortcode('just_icon');
	add_shortcode('just_icon', 'bii_SC_just_icon_shortcode');

	remove_shortcode('bsf-info-box');
	add_shortcode('bsf-info-box', 'bii_SC_icon_boxes');
});



add_action("bii_base_shortcodes", function() {
	?>
	<tr>
		<td><strong>[bii_displaywhenrequest cle="valeur"] contenu [/bii_displaywhenrequest]</strong></td>
		<td>Affiche contenu lorsque cle est égal à valeur (si valeur est égal à "all", alors contenu est affiché si cle existe)</td>
	</tr>
	<tr>
		<td><strong>[bii_notdisplaywhenrequest cle="valeur"] contenu [/bii_notdisplaywhenrequest]</strong></td>
		<td>Affiche contenu <strong>sauf</strong> lorsque cle est égal à valeur (si valeur est égal à "all", alors contenu n'est pas affiché si cle existe)</td>
	</tr>
	<tr>
		<td><strong>[bii_imageune] || [bii_imageune id="ID du post" size="full|large|medium|thumbnail"]
			</strong></td>
		<td>Affiche l'image à la une</td>
	</tr>
	<tr>
		<td><strong>[bii_loremipsum]</strong></td>
		<td>Génère du lorem ipsum</td>
	</tr>
	<tr>
		<td><strong>[bii_tmstp_match begin='timestamp begin' end='timestamp end']</strong></td>
		<td>Affiche contenu lorsque le timestamp actuel est compris entre begin et end. Si begin n'est pas défini alors begin = 0 si end n'est pas défini alors end = time + 60000</td>
	</tr>
    <tr>
		<td><strong>[filesOfFolder folder=## ]</strong></td>
		<td>Affiche l'ensemble des fichiers du dossier sélectionné</td>
	</tr>
	<?php
}, 1);


// get all files in a media folder : use with Real Media Library plugin
function bii_SC_getFilesOfFolder($atts) {
    
    $extToType = array(
        'application/pdf' => 'file-pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'file-docx',
        'application/msword' => 'file-doc',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'file-xlsx',
        'image/gif' => 'file-gif',
        'image/png' => 'file-png',
        'image/jpeg' => 'file-jpeg'
        );
    
    $atts = shortcode_atts(
        array(
            'folder' => false
        ), $atts, 'filesOfFolder' );
    
    //check if folder id is an integer
    if (intval($atts['folder'])) {
        
        //check if folder exist
        if (wp_rml_get_by_id($atts['folder'])) {
            
            //get files id into the folder
            $fileIDs = RML_Folder::sFetchFileIds($atts['folder']);
            if (count($fileIDs) > 0) {
                
                $files = get_posts(array( 'post__in' => $fileIDs , 'post_type' => 'attachment', 'numberposts' => -1));
                $nbrFiles = count($files);
                $i = 0;
                
                $contents = "<div class='container-fluid'><div class='row'>";
                foreach ($files as $file) {
                    if ($i % 6 == 0) {
                        $contents .= "</div><div class='row'>";
                    }
                    
                    $contents .= "<div class='col-md-2'>";
                    $contents .= "<a href='".$file->guid."' class='".$extToType[$file->post_mime_type]."' >".$file->post_title."</a>"; 
                    $contents .= "</div>";
                    $i++;
                }
                $contents .= "</div></div>";
                
                return $contents;
                
            }
            return "<p>Aucun fichier<p/>";
        }
    }
    return "<p>Dossier inexistant</p>";
}
add_shortcode("filesOfFolder", "bii_SC_getFilesOfFolder");