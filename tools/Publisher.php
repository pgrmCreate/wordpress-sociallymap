<?php

Class Publisher {
	public function publish($title, $content, $category=1, $publish_type='draft') {

		/*
		if($publish_type == "draft") {
			$read_more = "Lire la suite";
		}
		elseif ($publish_type == "publish") {
			$read_more = "<br><br>Lire la suite";
		}
		*/
		$listCats = [];
		if(is_array($category)) {
			foreach ($category as $key => $value) {
				$listCats[] = $value;
			}
		}

		$post = [
			'post_title' => $title,
			'post_content' => $content,
			'post_category' => $listCats,
			'post_status' => $publish_type,
		];
		
		//temporarily disable
		remove_filter('content_save_pre', 'wp_filter_post_kses');
		remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
		wp_insert_post($post, true);
		//bring it back once you're done posting
		add_filter('content_save_pre', 'wp_filter_post_kses');
		add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
	}
}