<?php

Class Publisher {
	public function publish($title, $content, $category=1, $isDraft=1) {
		if($isDraft === 0) $isDraft = 'publish';
		if($isDraft === 1) $isDraft = 'draft';

		$post = [
			'post_title' => $title,
			'post_content' => $content,
			'post_category' => [$category],
			'post_status' => $isDraft,
		];
		
		wp_insert_post($post, true);
	}
}