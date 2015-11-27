<?php
// If uninstall is not called from WordPress, exit
// if (!defined('WP_UNINSTALL_PLUGIN')) {
//     exit();
// }

global $wpdb;

$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."sm_options");
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."sm_entity_options");
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."sm_entities");
