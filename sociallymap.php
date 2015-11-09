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
        add_action('admin_menu', array($this, 'options_rss'));
        // _e("Page", 'sociallymap');
        $this->checkFluxRSS();
    }

    public function register_settings()
	{
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_linkRSS');
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_categorie');
	    register_setting('sociallymap_publisher_settings', 'sociallymap_publisher_isDraft');
	    register_setting('sociallymap_publisher_addRSS', 'sociallymap_addRSS_valid');
	    register_setting('sociallymap_publisher_addRSS', 'sociallymap_addRSS_value');
	    register_setting('sociallymap_publisher_addRSS', 'sociallymap_addRSS_listingRSS');
	}

	public function checkFluxRSS() {

	}

	public function add_admin_menu()
    {
        add_menu_page( 'Sociallymap publisher', 'Sociallymap', 'manage',
        'sociallymap-publisher', array($this, 'documentation_html'), 'dashicons-networking');

        add_submenu_page('sociallymap-publisher', 'Documentation', 'Documentation',
        'manage_options', 'sociallymap-documentation', array($this, 'documentation_html') );

        add_submenu_page('sociallymap-publisher', 'Configuration', 'Configuration',
        'manage_options', 'sociallymap-configuration', array($this, 'configuration_html') ); 

        add_submenu_page('sociallymap-publisher', 'Mes entités', 'Mes entités',
        'manage_options', 'sociallymap-rss-list', array($this, 'myEntities_html') ); 

        add_submenu_page('sociallymap-publisher', 'Ajouter une entité', 'Ajouter une entité',
        'manage_options', 'sociallymap-rss-add', array($this, 'addEntities_html') );         
    }

    public function configuration_html()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
        include('views/menu.php');
        include('views/configuration.php');
    }

    public function documentation_html ()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
        
        include('views/documentation.php');
    }

    public function myEntities_html ()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));

        include('views/menu.php');
        include('views/rss-list.php');
    }

    public function addEntities_html ()
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));

        include('views/menu.php');
        include('views/rss-add.php');
    }

    public function options_rss () {
		// $array_of_options = [];
		// update_option('sociallymap_addRSS_listingRSS', $array_of_options);

        echo("QQQQQQQQQQQQQQQQQQQQQ");
        echo(wp_get_current_user()->user_nicename);

		if($_POST['sociallymap_addRSS_valid']) {
			$array_of_options = [];

			if(get_option('sociallymap_addRSS_listingRSS')) {
				$array_of_options = get_option('sociallymap_addRSS_listingRSS');
			}

			$array_of_options[] = [
			'link' => $_POST['sociallymap_addRSS_value'],
			'category' => $_POST['sociallymap_publisher_categorie'],
			'author' => wp_get_current_user()->user_nicename
			];

			update_option('sociallymap_addRSS_listingRSS', $array_of_options);

			$linkToList = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-rss-list';
			wp_redirect($linkToList, 301 ); exit;
		}
    }
}

new Sociallymap_Plugin();
