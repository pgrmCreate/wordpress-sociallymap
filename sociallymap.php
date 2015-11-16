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

require_once(plugin_dir_path( __FILE__ ).'tools/templater.php');
require_once(plugin_dir_path( __FILE__ ).'tools/db-builder.php');
require_once(plugin_dir_path( __FILE__ ).'tools/publisher.php');
require_once(plugin_dir_path( __FILE__ ).'tools/Requester.php');
require_once(plugin_dir_path( __FILE__ ).'models/EntityCollection.php');
require_once(plugin_dir_path( __FILE__ ).'models/Entity.php');
require_once(plugin_dir_path( __FILE__ ).'models/Option.php');
require_once(plugin_dir_path( __FILE__ ).'models/ConfigOption.php');

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
            array_key_exists('sociallymap_deleteRSS', $_POST)  || 
            array_key_exists('sociallymap_updateRSS', $_POST)  ||
            array_key_exists('sociallymap_updateConfig', $_POST) ) {
                add_action('admin_menu', [$this, 'entityManager']);
        }

        add_action('admin_menu', [$this, 'add_admin_menu'] );
        add_action('init', [$this, 'rootingMapping'] );

        // Route for sociallymap
        add_action('parse_request', [$this, 'manageMessages'] );
    }

    public function add_admin_menu()
    {
        add_menu_page( 'Sociallymap publisher', 'Sociallymap', 'manage',
        'sociallymap-publisher', [$this, 'documentation_html'], 'dashicons-rss');

        add_submenu_page('sociallymap-publisher', 'Documentation', 'Documentation',
        'manage_options', 'sociallymap-documentation', [$this, 'documentationHtml'] );

        add_submenu_page('sociallymap-publisher', 'Configuration', 'Configuration',
        'manage_options', 'sociallymap-configuration', [$this, 'configurationHtml'] ); 

        add_submenu_page('sociallymap-publisher', 'Mes entités', 'Mes entités',
        'manage_options', 'sociallymap-rss-list', [$this, 'myEntitiesHtml'] );

        add_submenu_page(null, 'edit entity', 'Editer lien',
        'manage_options', 'sociallymap-rss-edit', [$this, 'editHtml'] ); 

        add_submenu_page('sociallymap-publisher', 'Ajouter une entité', 'Ajouter une entité',
        'manage_options', 'sociallymap-rss-add', [$this, 'addEntitiesHtml'] );         
    }

    public function rootingMapping () {
        add_filter( 'rewrite_rules_array','my_insert_rewrite_rules' );
        add_filter( 'query_vars','my_insert_query_vars' );
        add_action( 'wp_loaded','my_flush_rules' );

        // flush_rules() if our rules are not yet included
        function my_flush_rules(){
            $rules = get_option( 'rewrite_rules' );

            if ( ! isset( $rules['sociallymap/sm/getMessage'] ) ) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
            }
        }

            // Adding a new rule
        function my_insert_rewrite_rules( $rules )
        {
            $newrules = array();
            $newrules['sociallymap/sm/getMessage'] = 'index.php?sociallymap=$matches[1]&sm=$matches[2]&getMessage=$matches[3]';
            return $newrules + $rules;
        }

        // Adding the id var so that WP recognizes it
        function my_insert_query_vars( $vars )
        {
            array_push($vars, 'id', 'token');
            return $vars;
        }
    }

    public function manageMessages($wp) {
        $actions  = $wp->matched_rule;

        if(isset($_GET['id'])) $entityId = $_GET['id'];
        if(isset($_GET['token'])) $token = $_GET['token'];

        if($actions === "sociallymap/sm/getMessage" && isset($entityId) && isset($token)) {
            $curl      = new Requester();
            $publisher = new Publisher();
            $config = new ConfigOption();

            $configs = $config->getConfig();
            $jsonData  = $curl->launch();

            $readmore = $this->templater->loadReadMore($jsonData[0]['linkUrl'], $jsonData[0]['id']);

            foreach ($jsonData as $key => $value) {
                $title = $value['linkTitle'];
                if($key == 1) {
                    $contentArticle = $value['linkSummary'].$readmore;
                    $publisher->publish($title, $contentArticle , $configs[0]->default_value, $configs[2]->default_value);
                }
            }
        }
    }

    public function configurationHtml()
    {
        echo $this->templater->loadAdminPage('configuration.php');
    }

    public function documentationHtml ()
    {
        echo $this->templater->loadAdminPage('documentation.php');
    }

    public function myEntitiesHtml ()
    {
        $entitiesCollection = new EntityCollection();
        $loaderRequest = $entitiesCollection->all();
        $listRSS = [];
        $entity = new Entity();

        foreach ($loaderRequest as $datas) {
            foreach ($datas as $key => $value) {
                if($key == "id") {
                    $listRSS[] = $entity->getById($value);
                }
            }
        }

        echo $this->templater->loadAdminPage('rss-list.php', $listRSS);
        $messages = file_get_contents(plugin_dir_path( __FILE__ ).'messages.json');
        $json_a = json_decode($messages, true);
        $publisher = new Publisher();
    }

    public function addEntitiesHtml ()
    {
        $config = new ConfigOption();
        $configs = $config->getConfig();
        $data = new stdClass();

        foreach ($configs as $key => $value) {
            if($value->id == 1) {
                $data->category =  $value->default_value;
            }
        }

        echo $this->templater->loadAdminPage('rss-add.php', $data);
    }

    public function editHtml ()
    {
        $entity = new Entity;
        $editingEntity = $entity->getById($_GET['id']);

        echo $this->templater->loadAdminPage('rss-edit.php', $editingEntity);
    }

    public function entityManager () 
    {
        $entityCollection = new EntityCollection();
        $entityOption = new ConfigOption();
        $config = $entityOption->getConfig();

        // ACTION ENTITY : delete
        if(array_key_exists('sociallymap_deleteRSS', $_POST) && $_POST['sociallymap_deleteRSS']) {
            $idRemoving = $_POST['submit'];
            $entityCollection->deleteRowsByID($idRemoving);
        }

        // ACTION ENTITY : update
        if(array_key_exists('sociallymap_updateRSS', $_POST) && $_POST['sociallymap_updateRSS']) {
            if(!isset($_POST['sociallymap_activate'])) $_POST['sociallymap_activate'] = 0;
            if(!isset($_POST['sociallymap_publish_type'])) $_POST['sociallymap_publish_type'] = 'publish';

            $data = [
                'name'          => $_POST['sociallymap_label'],
                'category'      => $_POST['sociallymap_category'],
                'activate'      => $_POST['sociallymap_activate'],
                'display_type'  => $_POST['sociallymap_display_type'],
                'publish_type'  => $_POST['sociallymap_publish_type'],
                'id'            => $_GET['id'],
            ];

            $entityCollection->update($data);
        }


        // ACTION ENTITY : post
        if(array_key_exists('sociallymap_postRSS', $_POST) && $_POST['sociallymap_postRSS']) {
            if(!isset($_POST['sociallymap_activate'])) $_POST['sociallymap_activate'] = 0;
            if(!isset($_POST['sociallymap_publish_type'])) $_POST['sociallymap_publish_type'] = 'publish';

            $data = [
                'name'          => $_POST['sociallymap_name'],
                'category'      => $_POST['sociallymap_category'],
                'activate'      => $_POST['sociallymap_activate'],
                'publish_type'  => $config[2]->default_value,
                'display_type'  => $config[1]->default_value,
            ];

            $entityCollection->add($data);
        }

        // ACTION CONFIG : update
        if(array_key_exists('sociallymap_updateConfig', $_POST) && $_POST['sociallymap_updateConfig']) {
            if(!isset($_POST['sociallymap_publish_type'])) $_POST['sociallymap_publish_type'] = "publish";

            $data = [
                1 => $_POST['sociallymap_category'],
                2 => $_POST['sociallymap_display_type'],
                3 => $_POST['sociallymap_publish_type'],
            ];

            $currentConfig = new ConfigOption;
            $currentConfig->save($data);
        }

        $linkToList = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-rss-list';
        // wp_redirect($linkToList, 301 ); exit;  
    }
}

new Sociallymap_Plugin();
