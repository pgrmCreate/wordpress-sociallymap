<?php

class Requester {

	public function launch($entityId, $token) {
		$curl = curl_init();
		$urlCreator = [
			"baseUrl" 	=> "http://app.sociallymap-staging.com",
			"entityId" 	=> $entityId,
			"token"		=> $token,
		];
		$targetUrl = $urlCreator['baseUrl']."/raw-exporter/".$urlCreator['entityId'].
		"/feed?token=".$urlCreator['token'];

		$options = [
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL            => $targetUrl,
			CURLOPT_RETURNTRANSFER => true,
		];

		curl_setopt_array($curl, $options);
	
		$result = curl_exec($curl);
		$result = json_decode($result);

		// @TODO Vérifier qu'il n'y a pas de pb avec la reponse, sinon lever une exception (et la traiter)

		return $result;
	}


	
}