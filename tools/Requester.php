<?php

class Requester {

	public function launch() {
		$curl = curl_init();
		
		$options = [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL            => 'https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json',
			CURLOPT_RETURNTRANSFER => true,  
			CURLOPT_HTTPHEADER => ['Content-type: application/json']                                                  
		];

		curl_setopt_array($curl, $options);
	
		// $result = curl_exec($curl);

		$string = file_get_contents(plugin_dir_path( __FILE__ )."../messages.json");
		$return_value = json_decode($string, true);

		return $return_value;

		// return $result;
	}
}