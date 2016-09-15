<?php

function bii_action_links($links) {
	$links[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=bii_plugin_options')) . '">Paramètres</a>';
	if (get_option("bii_usedashboard")) {
		$links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=bii_plugin')) . '">Dashboard</a>';
	}
	return $links;
}

add_filter('plugin_action_links_' . plugin_basename(Bii_file), 'bii_action_links');
//ADD YOUR FILTERS AND ACTIONS
