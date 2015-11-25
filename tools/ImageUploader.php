<?php

class ImageUploader {
	public function upload($targetUrl) {
		$file = media_sideload_image($targetUrl, 0 );
		
		return $file;
	}

	public function tryUploadPost($defaultThumb, $media="", $mediaType="") {
		if ($mediaType == "photo" && !empty($media)) {
			return $this->upload($media);
		}
		else {
			return $this->upload($defaultThumb);
		}
	}
}