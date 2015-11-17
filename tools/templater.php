<?php
	class Templater {
		private $urlBase;

		public function __construct () {
			$this->urlBase =  dirname( __FILE__ ) . '/../views/';
		}

		public function loadAdminPage ($page, $data = null) {
			load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )) );
			wp_enqueue_style('configuration.css', plugin_dir_url( __FILE__ ).'../assets/styles/configuration.css');

			set_query_var('data', ['data' => $data]);

			ob_start();
				load_template($this->urlBase.'menu.php');
				load_template($this->urlBase.$page);
				$view = ob_get_contents();
			ob_end_clean();

			return $view;
		}

		public function loadReadMore($link, $id, $displayType) {
			load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )) );

			set_query_var('articleData', [
				'link' => $link,
				'id' => $id,
				'display_type' => $displayType
				]);

			ob_start();
				load_template($this->urlBase.'readmore_template.php');
				$readmore = ob_get_contents();
			ob_end_clean();


			return $readmore;
		}
}