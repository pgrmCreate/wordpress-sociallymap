<?php
	class Templater {
		public function load ($page) {
			load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
			load_template(dirname( __FILE__ ) . '/../views/'.'menu.php');
			load_template(dirname( __FILE__ ) . '/../views/'.'layout-templater.php');
			load_template(dirname( __FILE__ ) . '/../views/'.$page);
		}

		public function loadBlank ($page) {
			load_template(dirname( __FILE__ ) . '/../views/'.'layout-templater.php');
			load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
			load_template(dirname( __FILE__ ) . '/../views/'.$page);
		}
}