<?php

class FileDownloader
{
    private $currentExtension;

    public function download($url, $destinationFilename)
    {
        // check facebook
        $urlbase   = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);

        // check for facebook
        if ($urlbase == 'https://external.xx.fbcdn.net') {
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $params);
            $url = $params['url'];
        }

        // check relocation
        $watcher = $this->watchUrlLocation($url);
        $response = $watcher['url'];
        $responseCurl = $watcher['responseCurl'];

        $pathEncoded = '';
        // encode and add slash (excepte first part)


        // check header
        $checkerResponse = $this->checkResponseContentType($responseCurl);
        if (!$checkerResponse) {
            throw new fileDownloadException('Error Processing Request for '.$url, 1);
        }

        // Get extension for return
        $fileExtension = '.'.pathinfo($response, PATHINFO_EXTENSION);


        //CREATE FILE
        $fp = tempnam($destinationFilename, '');
        chmod($fp, 0766);
        $handle = fopen($fp.$this->currentExtension, 'w+');
        $bodyReponseCurl = $this->getBodyFromCurl($responseCurl);
        fwrite($handle, $bodyReponseCurl);
        fclose($handle);
        unlink($fp);

        return [
            'filename' => $fp,
            'extension' => $this->currentExtension,
        ];
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

            if (substr($contentType, 0, 6) == 'image/' || substr($contentType, 0, 6) == 'video/') {
                $acceptHeader = true;
                $this->currentExtension = '.'.substr($contentType, 6);
            } else {
                $messageException = 'ERROR DOWNLOAD : Header is not correct (not image or video)'.$contentType.' | url: '.$response;
                throw new fileDownloadException($messageException, 1);
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
        $isEncoded = false;
        $pathEncoded = '';
        while (true) {
            $responseCurl = $this->curlRequestWithHeader($url);
            $relocationUrl = $this->urlResponseRelocation($responseCurl);
            if ($relocationUrl) {
                $url = $relocationUrl;
                $url = urldecode($url);
                // var_dump($url);
                // die();
                $isEncoded = false;
            } else {
                break;
            }
        }

        $urlbase = $url;
        if ($isEncoded) {
            $pathEncoded = $url;
        } else {
            $fileUrlSplit = explode('/', parse_url($url, PHP_URL_PATH));
            for ($i=0; $i<count($fileUrlSplit); $i++) {
                ($i!=0) ? $slash = '/' : $slash = '';
                $pathEncoded .= $slash.urlencode($fileUrlSplit[$i]);
            }
        }

        $urlSend = parse_url($urlbase, PHP_URL_SCHEME).'://'.parse_url($urlbase, PHP_URL_HOST).$pathEncoded;
        $responseCurl = $this->curlRequestWithHeader($urlSend);
        $relocationUrl = $this->urlResponseRelocation($responseCurl);

        return [
            'url' => $urlSend,
            'responseCurl' => $responseCurl,
        ];
    }
}
