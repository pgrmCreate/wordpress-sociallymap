<?php

class FileDownloader
{
    // DONE - @todo destinationFilename rename
    // + 1 param content type (with const for multi type)
    // @todo add class fileDownloader + add class verifier (curl result : contenttype + status) + check content type from url
    public function download($url, $destinationFilename)
    {
        // Parse Url
        $parts = parse_url($url);
        $urlbase = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
        $keyAccept = isset($parts['query']) ? 'query' : 'path';
        parse_str($parts[$keyAccept], $query);

        Logger::info("Process uploading image", [$parts, $url]);

        // check for facebook
        if ($urlbase == 'https://external.xx.fbcdn.net') {
            $url = $query['url'];
            Logger::alert("Download facebook (try get url from GETVAR)", $query['url']);
        }

        // @todo revoir regex (use explode)
        // GET EXTENSION FILE
        $acceptedFile = ['jpeg', 'jpg', 'jpe', 'gif', 'png'];
        $headerReturned = get_headers($url, 1);
        if (!isset($headerReturned['Content-Type'])) {
            if (substr($headerReturned['Content-Type'], 0, 6) === 'images/') {
                throw new Exception('request don\'t give a content-type image');
            }
        }

        $fileCuted = explode('.', $url);
        $filename = $destinationFilename[count(explode('/', $destinationFilename))];
        $currentExtension = $filename.'.'.$fileCuted[count($fileCuted)-1];
        // SET NAME OF FILE
        $currentFileName = $destinationFilename.$currentExtension;
        // set local location
        $currentLinkLocation = plugin_dir_path(__FILE__).'../tmp/'.$currentFileName;

        $ch = curl_init($url);
        //CREATE FILE
        $fp = fopen($currentLinkLocation, 'w+');
        // download FILE WITH REQUEST
        // bonus : @todo relocation, get 300 status => go new request to new url & del folow location option curl
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_close($ch);
        fclose($fp);

        $file_array = [];
        $file_array['name'] = $currentFileName;

        // Download file to temp location.
        $file_array['tmp_name'] = $currentLinkLocation;

        // Do the validation and storage stuff.
        $id = media_handle_sideload($file_array, 0, null);

        $src = wp_get_attachment_url($id);

        // @todo throw
        if (gettype($src) != "string") {
            Logger::error("ERROR DOWNLOAD", $src);
            return $src;
        }

        return $src;
    }
}
