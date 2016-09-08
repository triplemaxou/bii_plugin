jQuery(function ($) {
//	bii_CL("restrict");
	var tohide = "";
	if (bii_role != "admin") {
		tohide += "#wp-admin-bar-new-ignition_product, #wp-admin-bar-wpseo-menu, #toplevel_page_vc-welcome, #wp-admin-bar-premiothemes-comingsoon-notice"
			+"#biipreload, #mymetabox_revslider_0, #protect_content, #shortcode_meta, #bii_RCContent";
	}
	$(tohide).hide(0);
});