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

        $allExeptLast = explode('/', $targetUrl);
        array_pop($allExeptLast);
        $targetUrl = preg_replace_callback('#https?://.+/([^?]+)#', function ($match) {
            return join('/', array_map('rawurlencode', explode('/', $match[1])));
        }, $targetUrl);

        $targetUrl = implode("/", $allExeptLast).'/'.$targetUrl;

        error_log(PHP_EOL.'# TRY UPLOAD => '.$targetUrl.PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');
        $file = $this->uploadWordpress($targetUrl, 0, null, 'src');

        error_log(PHP_EOL.'# RESULT UPLOAD => '.print_r($file, true).PHP_EOL, 3, plugin_dir_path(__FILE__).'../logs/error.log');

        return $file;
    }


    public function uploadWordpress($file, $post_id, $desc = null, $return = 'html')
    {
        if (! empty($file)) {

            // Set variables for storage, fix file filename for query strings.
            preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
            if (! $matches) {
                return new WP_Error('image_sideload_failed', __('Invalid image URL'));
            }

            $file_array = [];
            $file_array['name'] = "";
            foreach ($matches as $key => $value) {
                if($key != 0) {
                    $file_array['name'] .= $value;
                }
            }

            // Download file to temp location.
            $file_array['tmp_name'] = download_url($file);

            // If error storing temporarily, return the error.
            if (is_wp_error($file_array['tmp_name'])) {
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.
            $id = media_handle_sideload($file_array, $post_id, $desc);

            // If error storing permanently, unlink.
            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            $src = wp_get_attachment_url($id);
        }

        // Finally, check to make sure the file has been saved, then return the HTML.
        if (! empty($src)) {
            if ($return === 'src') {
                return $src;
            }

            $alt = isset($desc) ? esc_attr($desc) : '';
            $html = "<img src='$src' alt='$alt' />";
            return $html;
        } else {
            return new WP_Error('image_sideload_failed');
        }
    }
}
