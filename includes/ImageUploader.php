<?php

class ImageUploader
{
    public function upload($targetUrl)
    {
        $parts = parse_url($targetUrl);
        parse_str($parts['query'], $query);

        if (isset($query['url'])) {
            $targetUrl = $query['url'];
        }

        $file = media_sideload_image($targetUrl, 0, null, 'src');

        return $file;
    }
}
