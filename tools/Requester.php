<?php

class Requester {

	public function launch($entityId, $token) {
		// echo("# INIT PROCESS # <br>");

		$curl = curl_init();
		$urlCreator = [
		"baseUrl" 	=> "http://app.sociallymap.local",
		"entityId" 	=> $entityId,
		"token"		=> $token,
		];
		$targetUrl = $urlCreator['baseUrl']."/raw-exporter/".$urlCreator['entityId'].
		"/feed?token=".$urlCreator['token'];

		$targetUrl = "http://app.sociallymap.local/emulatorSM/";
		// echo('<b>link : </b>'.$targetUrl.'<br>');

		$options = [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL            => $targetUrl,
			CURLOPT_RETURNTRANSFER => true,  
			// CURLOPT_HTTPHEADER     => ['Content-type: application/json']                                                  
		];

		curl_setopt_array($curl, $options);
	
		$result = curl_exec($curl);

		$result = json_decode($result);

		return $result;
	}


	
}