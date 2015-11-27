<?php
	require_once(plugin_dir_path( __FILE__ ).'includes/DbBuilder.php');

	$dbBuilder = new DbBuilder();
	$dbBuilder->destroyAll();
    