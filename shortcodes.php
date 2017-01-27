<?php
// ADD YOUR SHORTCODES HERE
function bii_current_year(){
	return date("Y");
}
add_shortcode("current-year", "bii_current_year");
