<?php

Class Publisher {
	public function publish($title, $content, $category=1, $publish_type='draft') {
		if($publish_type === 0) $publish_type = 'publish';
		if($publish_type === 1) $publish_type = 'draft';

		/*
		if($publish_type == "draft") {
			$read_more = "Lire la suite";
		}
		elseif ($publish_type == "publish") {
			$read_more = "<br><br>Lire la suite";
		}
		*/

		$post = [
			'post_title' => $title,
			'post_content' => $content,
			'post_category' => [$category],
			'post_status' => $publish_type,
		];
		
		wp_insert_post($post, true);
	}
}