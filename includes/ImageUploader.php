<?php

class ImageUploader
{
    public function upload($targetUrl)
    {
        $parts = parse_url($targetUrl);
        $urlbase = parse_url($targetUrl, PHP_URL_SCHEME).'://'.parse_url($targetUrl, PHP_URL_HOST);
        parse_str($parts['query'], $query);

        error_log(PHP_EOL.'# Base url ? => '.$urlbase.PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');

        if (isset($query['url']) && $urlbase == 'https://external.xx.fbcdn.net') {
            $targetUrl = $query['url'];
            error_log(PHP_EOL.'# WARNING UPLOAD #'.PHP_EOL.'UPLOAD '.$query['url'].PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');
        }

        if (!gettype($file) == "string") {
            error_log(PHP_EOL.'# ERROR UPLOAD #'.PHP_EOL.'MESSAGE ERR : '.print_r($file->errors, true).PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');
        }

        $targetUrl = urlencode($targetUrl);
        error_log(PHP_EOL.'# TRY UPLOAD => '.$targetUrl.PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');
        $file = media_sideload_image($targetUrl, 0, null, 'src');

        return $file;
    }
}
