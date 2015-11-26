<?php

class Requester
{
    public function launch($entityId, $token)
    {
        $curl = curl_init();

    // @TODO Retrieve the right url's depending the environement
        $urlCreator = [
            'baseUrl' => $_ENV["URL_SOCIALLYMAP"],
            'entityId'=> $entityId,
            'token'   => $token,
        ];
        $targetUrl = $urlCreator['baseUrl'].'/raw-exporter/'.$urlCreator['entityId'].'/feed?token='.$urlCreator['token'];

        $options = [
            // Return the transfer, don't display it
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_URL            => $targetUrl,
        ];

        curl_setopt_array($curl, $options);
    
        $result = curl_exec($curl);
        $requestInfos = curl_getinfo($curl);

        // Close the curl session and free allocated memory
        curl_close($curl);

        try {
            // If the response isn't a string
            if (! $result) {
                throw new Exception('Wrong response format', 1);
            }

            // Decode the JSON response
            $result = json_decode($result);

            // The request failed
            if ($requestInfos['http_code'] !== 200) {
                throw new Exception($result->message, 1);
            }
        } catch (Exception $e) {
            header("HTTP/1.0 502 Bad Gateway");
            error_log('Sociallymap: Error during retrieving entity pending messages');
            error_log('# Error: '.$e->getMessage().' #');
            exit;
        }
        

        return $result;
    }
}
