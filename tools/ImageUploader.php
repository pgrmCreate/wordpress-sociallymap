<?php

class ImageUploader {
	public function upload($targetUrl = "https://upload.wikimedia.org/wikipedia/commons/8/8c/JPEG_example_JPG_RIP_025.jpg") 
	{
		$file = media_sideload_image($targetUrl, 0 );
		
		return $file;
	}

	public function tryUploadPost($defaultThumb, $media="", $mediaType="") {
		if($mediaType == "photo" && !empty($media) ) {
			return $this->upload($media);
		}
		else {
			return $this->upload($defaultThumb);
		}
	}
}