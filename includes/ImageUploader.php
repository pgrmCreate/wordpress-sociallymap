<?php

class ImageUploader
{
    public function upload($targetUrl)
    {
        $file = media_sideload_image($targetUrl, 0, null, 'src');

        error_log(print_r($file, true), 3, plugin_dir_path(__FILE__).'../logs/error.log');

        return $file;
    }
}
