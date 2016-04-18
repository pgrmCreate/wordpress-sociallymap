<?php

class VideoDownloader
{

    public function upload($url)
    {
        $tmp = download_url($url);

        $file_array = [
            'name'     => basename($url),
            'tmp_name' => $tmp
        ];

        // Check for download errors
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        $id = media_handle_sideload($file_array, 0);
        // Check for handle sideload errors.
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return false;
        }

        $attachment_url = wp_get_attachment_url($id);

        return $attachment_url;
        // Do whatever you have to here
    }
}
