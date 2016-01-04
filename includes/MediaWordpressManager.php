<?php

class MediaWordpressManager
{
    public function integrateMediaToWordpress($temporyFile, $fileExtension)
    {
        $temporyFile = $temporyFile;
        $file_array = [];

        // Extract folder & filename
        $tabUrl = explode("/", $temporyFile);
        $filename = $tabUrl[count($tabUrl)-1];

        $file_array['name'] = $filename.$fileExtension;
        $file_array['tmp_name'] = $temporyFile;


        // Do the validation and storage stuff.
        $id = media_handle_sideload($file_array, 0);

        $src = wp_get_attachment_url($id);
        // @todo throw
        if (gettype($src) != "string") {
            throw new fileDownloadException("ERROR DOWNLOAD FOR ".$src, 1);
            return $src;
        } else {
            return $src;
        }
    }
}
