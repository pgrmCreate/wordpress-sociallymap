<?php

// TEST BOOTSTRAP

define('ROOT_DIR', __DIR__);
define('TEMP_DIR', ROOT_DIR . 'tmp');

require_once(ROOT_DIR.'/../includes/FileDownloader.php');
require_once(ROOT_DIR.'/../includes/Logger.php');
require_once(ROOT_DIR.'/../includes/MediaWordpressManager.php');

require_once(ROOT_DIR.'/exception/fileDownloadException.php');
require_once(ROOT_DIR.'/exception/fileDownloadException.php');

require_once(ROOT_DIR . "/../../../../". "wp-load.php");
