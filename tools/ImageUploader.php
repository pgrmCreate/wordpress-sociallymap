<?php

class ImageUploader
{
    public function upload($targetUrl)
    {
        $file = media_sideload_image($targetUrl, 0);
        
        return $file;
    }
}
