<?php
/*
Plugin Name: Sociallymap
pPlugin URI: http://LoadDis-plugin.com
Description: Un plugin permettant l'affichage de flux RSS
Version: 0.1
Author: Midnight Alhena
Author URI: http://alhena-conseil.com/
License: GPL2
*/

include_once plugin_dir_path( __FILE__ ).'publisherWidget.php';


class Sociallymap_Plugin
{
    public function __construct()
    {
    	wp_register_style( 'configuration-style', plugins_url( '/views/styles/configuration.css', __FILE__ ),
         array(), '20120208', 'all' );

        add_action('widgets_init', function(){register_widget('Sociallymap_publisher_Widget');});
        add_action('admin_init', array($this, 'register_settings'));
        // _e("Page", 'sociallymap');
        $this->checkFluxRSS();
    }

    public function register_settings()
	{
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_linkRSS');
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_categorie');
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_isActive');
	}

	public function checkFluxRSS() {

	}
}

new Sociallymap_Plugin();
