<?php

function bii_enqueue_scripts() {
	$bii_provider = get_option("bii_provider");
	$style_biilink = $bii_provider . '/globalbiilink.css';
	$script_biilink = $bii_provider . '/globalbiilink.js';
	$ddslick = $bii_provider . '/ddSlick.js';
	$ui = $bii_provider . '/jqueryui.js';
	$bodyclass = apply_filters("body_class", "");
	

	if (headersOK($style_biilink)) {
		wp_enqueue_style('globalbiilink-css', $style_biilink);
	}
	if (headersOK($script_biilink)) {
		wp_enqueue_script('ddslick', $ddslick, array('jquery'));

		if (in_array("bii_page_perso_edit", $bodyclass)) {
			wp_enqueue_script('bii_jqueryui', $ui, array('jquery'));
			wp_enqueue_script('globalbiilink-js', $script_biilink, array('jquery', 'util', 'bii_jqueryui'));
			
//			wp_dequeue_style("nm_filemanager-nm-ui-style-css"); //Affichage datetimepicker
		}else{
			wp_enqueue_script('globalbiilink-js', $script_biilink, array('jquery', 'util'));
		}
	}
}

function bii_plugin_test_zone(){
	
}

function bii_add_favicon() {
	$instance_id = apply_filters("bii_shared_items_my_instance_id");
  	$favicon_url = Bii_url . 'img/favicon'.$instance_id.'.ico';
	echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
}
  
add_action('admin_head', 'bii_add_favicon');

add_action('wp_enqueue_scripts', 'bii_enqueue_scripts');
add_action('bii_plugin_test_zone', 'bii_plugin_test_zone');
