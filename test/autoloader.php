<?php

// TEST BOOTSTRAP

define('ROOT_DIR', __DIR__);
define('TEMP_DIR', ROOT_DIR . 'tmp');

require_once(ROOT_DIR.'/../includes/FileDownloader.php');
require_once(ROOT_DIR.'/../includes/Logger.php');
require_once(ROOT_DIR.'/exception/fileDownloadException.php');

function plugin_dir_path($file = "")
{
    $rootPath = ROOT_DIR.'/';

    return $rootPath;
}

function media_handle_sideload()
{
    return 0;
}

function wp_get_attachment_url()
{
    return 0;
}

