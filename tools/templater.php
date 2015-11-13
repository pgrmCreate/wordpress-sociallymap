<?php
	class Templater {
		public function load ($page, $data = null) {
			load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )) );

			$urlBase = dirname( __FILE__ ) . '/../views/';

			set_query_var('data', ['data' => $data]);

			load_template($urlBase.'menu.php');
			load_template($urlBase.'layout-templater.php');
			load_template($urlBase.$page);
		}
}