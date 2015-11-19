<?php

class ImageUploader {
	public function upload($targetUrl = "") 
	{
		if($targetUrl != "") {
			$targetUrl = "https://upload.wikimedia.org/wikipedia/commons/8/8c/JPEG_example_JPG_RIP_025.jpg";
		}
		// $uploaddir = wp_upload_dir();
		// $uploadfile = $uploaddir['path'] . '/' . 'uneimage.jpg';

		// $contents= file_get_contents('https://upload.wikimedia.org/wikipedia/commons/1/1e/Stonehenge.jpg');
		// $savefile = fopen($uploadfile, 'w');
		// fwrite($savefile, $contents);
		// fclose($savefile);
		$file = media_sideload_image($targetUrl, 0 );
		print_r($file);
	}
}