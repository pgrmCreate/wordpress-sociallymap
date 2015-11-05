<?php

class Sociallymap_publisher_Widget extends WP_Widget
{
    private $configWidget;

    public function __construct()
    {
        parent::__construct('SociallyMap_publisher', 'Sociallymap : Publisher',
        	array('description' => 'Un outil permettant de publier automatiquement depuis Sociallymap'));


        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public function widget($args, $instance)
    {
        echo 'widget publisher<br>';
    }

    public function add_admin_menu()
    {
        add_menu_page( 'Sociallymap publisher', 'Sociallymap', 'manage_options',
        'sociallymapPublisher', array($this, 'documentation_html'), 'dashicons-networking');

        add_submenu_page( 'sociallymapPublisher', 'Configuration', 'Configuration',
        'manage_options', 'sociallymap-configuration', array($this, 'configuration_html') ); 
    }

    public function form($instance)
    {
        load_plugin_textdomain('sociallymap', false, "../".basename(dirname( __FILE__ )));
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
}
