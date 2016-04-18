<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

require_once(plugin_dir_path(__FILE__).'includes/DbBuilder.php');

$dbBuilder = new DbBuilder();
$dbBuilder->destroyAll();
