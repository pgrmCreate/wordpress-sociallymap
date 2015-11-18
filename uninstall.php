<?php
	require_once(plugin_dir_path( __FILE__ ).'tools/db-builder.php');

	$dbBuilder = new DbBuilder();
	$dbBuilder->destroyAll();
    