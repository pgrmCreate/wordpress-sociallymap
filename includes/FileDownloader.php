<?php

class FileDownloader
{
    public function download($url, $destinationFilename)
    {
        // check relocation
        $watcher = $this->watchUrlLocation($url);
        $response = $watcher['url'];
        $responseCurl = $watcher['responseCurl'];
        $isEncoded = false;

        $pathEncoded = "";
        $urlbase   = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
        // encode and add slash (excepte first part)
        if ($isEncoded) {
            $pathEncoded = $url;
        } else {
            $fileUrlSplit = explode('/', parse_url($url, PHP_URL_PATH));
            for ($i=0; $i<count($fileUrlSplit); $i++) {
                ($i!=0) ? $slash = '/' : $slash = '';
                $pathEncoded .= $slash.urlencode($fileUrlSplit[$i]);
            }
        }

        // check for facebook
        if ($urlbase == 'https://external.xx.fbcdn.net') {
            $url = $query['url'];
            Logger::alert("Download facebook (try get url from GETVAR)", $query['url']);
        }

        // check header
        if (!$this->checkResponseContentType($responseCurl)) {
            throw new fileDownloadException("Error Processing Request for ".$url, 1);
        }

        $fileCuted = explode('.', $url);
        $filename = $destinationFilename[count(explode('/', $destinationFilename))];
        $currentExtension = $filename.'.'.$fileCuted[count($fileCuted)-1];
        // SET NAME OF FILE
        $currentFileName = $destinationFilename.$currentExtension;
        // set local location
        $currentLinkLocation = plugin_dir_path(__FILE__).$destinationFilename;

        //CREATE FILE
        $fp = fopen($destinationFilename, 'w+');
        fwrite($fp, $this->getBodyFromCurl($responseCurl));
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

    private function curlRequestWithHeader($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @return string
     */
    private function urlResponseRelocation($response)
    {
        $headers = $this->getHeaderFromCurl($response);

        $urlRelocation = false;
        if (isset($headers['Location'])) {
            $urlRelocation = $headers['Location'];
        }

        return $urlRelocation;
    }

    /**
     * @return boolean
     */
    private function checkResponseContentType($response)
    {
        $headers = $this->getHeaderFromCurl($response);

        $acceptHeader = false;
        if (isset($headers['Content-Type'])) {
            $contentType = $headers['Content-Type'];

            if (substr($contentType, 0, 6) == "image/") {
                $acceptHeader = true;
            }
        }

        return $acceptHeader;
    }

    // INPUT => curl response OUTPUT => Array header
    private function getHeaderFromCurl($response)
    {
        $headers = array();

        $headerText = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $headerText) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    private function getBodyFromCurl($response)
    {
        $body = substr($response, strpos($response, "\r\n\r\n"));
        $body = substr($body, 4);

        return $body;
    }

    /**
     * @return Array
     */
    private function watchUrlLocation($url)
    {
        while (true) {
            $responseCurl = $this->curlRequestWithHeader($url);
            $relocationUrl = $this->urlResponseRelocation($responseCurl);
            if ($relocationUrl) {
                $url = $relocationUrl;
                // $url = urldecode($url);
                var_dump($url);
                // die();
                // $isEncoded = true;
            } else {
                break;
            }
        }

        return [
            'url' => $url,
            'responseCurl' => $responseCurl,
        ];
    }
}
