<?php

class Requester
{
    public function launch($entityId, $token, $environement)
    { 
        error_log('Ping received: '.print_r([$entityId, $token, $environement], true), 3, plugin_dir_path(__FILE__)."../logs/error.log");

        if (!is_callable('curl_init')) {
            error_log("Curl no exist, request impossible..", 3, plugin_dir_path(__FILE__)."../logs/error.log");
            header("HTTP/1.0 501 Not Implemented");
            exit("Curl request impossible for wordpress server");
        }
 
        $curl = curl_init();

        $envtype = $_ENV['URL_SOCIALLYMAP'];
        $envtype = $envtype[$environement];
        error_log("Actuel target environnement (with base #".$environement."#): ".print_r($envtype, true), 3, plugin_dir_path(__FILE__)."../logs/error.log");

        $urlCreator = [
            'baseUrl' => $envtype,
            'entityId'=> $entityId,
            'token'   => $token,
        ];
        $targetUrl = $urlCreator['baseUrl'].'/raw-exporter/'.$urlCreator['entityId'].
        '/feed?token='.$urlCreator['token'];

        $options = [
            // Return the transfer, don't display it
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_URL            => $targetUrl,
        ];

        curl_setopt_array($curl, $options);
    
        // get on UTF8
        header('Content-type: text/html; charset=UTF-8');
        
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
            error_log('Sociallymap: Error during retrieving entity pending messages', 3, plugin_dir_path(__FILE__)."../logs/error.log");
            error_log('# Error: '.$e->getMessage().' #', 3, plugin_dir_path(__FILE__)."../logs/error.log");
            exit;
        }
        

        return $result;
    }
}
