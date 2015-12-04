<?php

class ImageUploader
{
    public function upload($targetUrl)
    {
        $parts = parse_url($targetUrl);
        parse_str($parts['query'], $query);

        error_log(PHP_EOL.'# WARNING UPLOAD #'.PHP_EOL.'UPLOAD '.$parts['query'].PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');
        if (isset($query['url'])) {
            $targetUrl = $query['url'];
        }

        $file = media_sideload_image($targetUrl, 0, null, 'src');

        return $file;
    }
}
