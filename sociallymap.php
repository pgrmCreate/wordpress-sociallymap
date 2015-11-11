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

require_once(plugin_dir_path( __FILE__ ).'publisherWidget.php');
require_once(plugin_dir_path( __FILE__ ).'tools/templater.php');
require_once(plugin_dir_path( __FILE__ ).'tools/db-builder.php');
require_once(plugin_dir_path( __FILE__ ).'tools/publisher.php');
require_once(plugin_dir_path( __FILE__ ).'models/EntityCollection.php');
require_once(plugin_dir_path( __FILE__ ).'models/Entity.php');
require_once(plugin_dir_path( __FILE__ ).'models/Option.php');

class Sociallymap_Plugin
{
    private $wpdb;
    private $templater;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->templater = new Templater();

        $builder = new DbBuilder();
        $builder->dbInitialisation();

        if(array_key_exists('sociallymap_postRSS', $_POST)      || 
            array_key_exists('sociallymap_deleteRSS',  $_POST)  || 
            array_key_exists('sociallymap_updateRSS',  $_POST)  ||
            array_key_exists('sociallymap_updateConfig',  $_POST) ) {
                add_action('admin_menu', [$this, 'entityManager']);
        }

        add_action('admin_menu', [$this, 'add_admin_menu'] );
    }

    public function add_admin_menu()
    {
        add_menu_page( 'Sociallymap publisher', 'Sociallymap', 'manage',
        'sociallymap-publisher', [$this, 'documentation_html'], 'dashicons-networking');

        add_submenu_page('sociallymap-publisher', 'Documentation', 'Documentation',
        'manage_options', 'sociallymap-documentation', [$this, 'documentation_html'] );

        add_submenu_page('sociallymap-publisher', 'Configuration', 'Configuration',
        'manage_options', 'sociallymap-configuration', [$this, 'configuration_html'] ); 

        add_submenu_page('sociallymap-publisher', 'Mes entités', 'Mes entités',
        'manage_options', 'sociallymap-rss-list', [$this, 'myEntities_html'] );

        add_submenu_page(null, 'edit entity', 'Editer lien',
        'manage_options', 'sociallymap-rss-edit', [$this, 'edit_html'] ); 

        add_submenu_page('sociallymap-publisher', 'Ajouter une entité', 'Ajouter une entité',
        'manage_options', 'sociallymap-rss-add', [$this, 'addEntities_html'] );         
    }

    public function configuration_html()
    {
        $this->templater->loadBlank('configuration.php');
    }

    public function documentation_html ()
    {
        $this->templater->loadBlank('documentation.php');
    }

    public function myEntities_html ()
    {
        $this->templater->load('rss-list.php');
    }

    public function addEntities_html ()
    {
        $this->templater->load('rss-add.php');
    }

    public function edit_html ()
    {
        $this->templater->load('rss-edit.php');
    }

    public function entityManager () 
    {
        $entityCollection = new EntityCollection();

        // ACTION ENTITY : delete
        if(array_key_exists('sociallymap_deleteRSS', $_POST) && $_POST['sociallymap_deleteRSS']) {
            $idRemoving = $_POST['submit'];
            $entityCollection->deleteRowsByID($idRemoving);
        }

        // ACTION ENTITY : update
        if(array_key_exists('sociallymap_updateRSS', $_POST) && $_POST['sociallymap_updateRSS']) {
            if(!isset($_POST['sm_active'])) $_POST['sm_active'] = 0;
                $data = [
                'name'     => $_POST['sm_label'],
                'category' => $_POST['sm_category'],
                'activate' => $_POST['sm_active'],
                'modal_mobile' => $_POST['sm_modal_mobile'],
                'modal_desktop' => $_POST['sm_modal_desktop'],
                'id'       => $_GET['id'],
            ];

            $entityCollection->update($data);
        }

        // ACTION ENTITY : post
        if(array_key_exists('sociallymap_postRSS', $_POST) && $_POST['sociallymap_postRSS']) {
            print_r($_POST);
            $data = [
                'name'     => $_POST['sociallymap_name'],
                'category' => $_POST['sociallymap_category'],
                'activate' => $_POST['sociallymap_activate'],
                'modal_mobile' => $_POST['sociallymap_modal_mobile'],
                'modal_desktop' => $_POST['sociallymap_modal_desktop'],
            ];
            $entityCollection->add($data);
        }

        $linkToList = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-rss-list';
        // wp_redirect($linkToList, 301 ); exit;  
    }
}

new Sociallymap_Plugin();
