<?php

class ImageUploader
{
    public function upload($targetUrl)
    {
        $parts = parse_url($targetUrl);
        $urlbase = parse_url($targetUrl, PHP_URL_SCHEME).'://'.parse_url($targetUrl, PHP_URL_HOST);
        $urlbase = parse_url($targetUrl, PHP_URL_SCHEME).'://'.parse_url($targetUrl, PHP_URL_HOST);
        parse_str($parts['query'], $query);

        error_log(PHP_EOL.'# Base url ? => '.$urlbase.PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');

        if (isset($query['url']) && $urlbase == 'https://external.xx.fbcdn.net') {
            $targetUrl = $query['url'];
            // encode file name
            $parts = parse_url($targetUrl);
            if (!empty($parts['path'])) {
                $parts['path'] = join('/', array_map('rawurlencode', explode('/', $parts['url'])));
            }
            $targetUrl = http_build_url($parts);
        }

        if (!gettype($file) == "string") {
            error_log(PHP_EOL.'# ERROR UPLOAD #'.PHP_EOL.'MESSAGE ERR : '.print_r($file->errors, true).PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');
        }

        $file = media_sideload_image($targetUrl, 0, null, 'src');

        return $file;
    }
}
