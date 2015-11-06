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

        // add_action('widgets_init', function(){register_widget('Sociallymap_publisher_Widget');});
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        // _e("Page", 'sociallymap');
        $this->checkFluxRSS();
    }

    public function register_settings()
	{
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_linkRSS');
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_categorie');
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_isDraft');
	}

	public function checkFluxRSS() {

	}

	public function add_admin_menu()
    {
        add_menu_page( 'Sociallymap publisher', 'Sociallymap', 'manage_options',
        'sociallymapPublisher', array($this, 'documentation_html'), 'dashicons-networking');

        add_submenu_page('sociallymapPublisher', 'Configuration', 'Configuration',
        'manage_options', 'sociallymap-configuration', array($this, 'configuration_html') ); 

        add_submenu_page('sociallymapPublisher', 'Mes RSS', 'Mes RSS',
        'manage_options', 'sociallymap-rss', array($this, 'mesRSS_html') ); 
    }

        public function configuration_html()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
        include('views/configuration.php');
    }

    public function documentation_html ()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
        
        include('views/documentation.php');
    }

        public function mesRSS_html ()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
        
        include('views/mes-RSS.php');
    }

}

new Sociallymap_Plugin();
