<?php
/*
Plugin Name: Sociallymap
Plugin URI: http://LoadDis-plugin.com
Description: Un plugin permettant l'affichage de flux RSS
Version: 0.9
Author: Midnight Alhena
Author URI: http://alhena-conseil.com/
License: GPL2
*/

require_once(plugin_dir_path( __FILE__ ).'tools/Templater.php');
require_once(plugin_dir_path( __FILE__ ).'tools/Db-builder.php');
require_once(plugin_dir_path( __FILE__ ).'tools/Publisher.php');
require_once(plugin_dir_path( __FILE__ ).'tools/Requester.php');
require_once(plugin_dir_path( __FILE__ ).'tools/ImageUploader.php');
require_once(plugin_dir_path( __FILE__ ).'tools/GithubUpdater.php');
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

        // register_uninstall_hook('uninstall.php', 'smUninstallPlugin');

        $this->templater = new Templater();

        $builder = new DbBuilder();
        $builder->dbInitialisation();

        if(array_key_exists('sociallymap_postRSS', $_POST)     || 
            array_key_exists('sociallymap_deleteRSS', $_POST)  || 
            array_key_exists('sociallymap_updateRSS', $_POST)  ||
            array_key_exists('sociallymap_updateConfig', $_POST) ) {
                add_action('admin_menu', [$this, 'entityManager']);
        }

        // Route for sociallymap
        add_action('parse_request', [$this, 'manageMessages'] );

        add_action('admin_menu', [$this, 'add_admin_menu'] );
        add_action('admin_menu', [$this, 'githubConfiguration'] );
        add_action('init', [$this, 'rootingMapping'] );
        add_filter('the_content',[$this, "my_post_footer"]);
    }

    public function my_post_footer($content){
        global $post;
        $config = new ConfigOption();
        $configs = $config->getConfig();

        if($configs[1]->default_value == "tab") {
            $footer = "<p data-hidden-display>tab</p>";
        }
        else {
            $footer = "<p data-hidden-display>modal</p>";
        }

        if ($footer){
          return $content . $footer;
        }
        return $content;
    }

    public function add_admin_menu()
    {        
        add_menu_page( 'Sociallymap publisher', 'Sociallymap', 'manage',
        'sociallymap-publisher', [$this, 'documentation_html'], plugin_dir_url( __FILE__ ).'assets/images/logo.png');

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
        $this->loadAssets(true);

        add_filter( 'rewrite_rules_array','my_insert_rewrite_rules' );
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

 
    }

    public function manageMessages($wp) {
        $actions  = $wp->matched_rule;

        // var_dump($_POST);

        if(!isset($_POST['entityId']) || !isset($_POST['token']) || $actions !== "sociallymap/sm/getMessage") {
            return false;
        }

        $curl      = new Requester();
        $publisher = new Publisher();
        $config = new ConfigOption();

        $configs = $config->getConfig();
        $jsonData  = $curl->launch($_POST['entityId'], $_POST['token']);

        ?> <div styles="position: absolute; background: white; z-index: 9999999;"> <?php
        foreach ($jsonData as $key => $value) {
            $title = $value->linkTitle;
            $readmore = $this->templater->loadReadMore($value->linkUrl, $value->id, $configs[1]->default_value);
            if($readmore) {
                $contentArticle = '<p>'.$value->linkSummary.'</p>'.$readmore;
                $publisher->publish($title, $contentArticle , $configs[0]->default_value, $configs[2]->default_value);
            }
        }
        ?> </div> <?php

    }

    public function configurationHtml()
    {
        $this->loadAssets();
        $data = [];
        if(array_key_exists('sociallymap_updateConfig', $_POST) ) {
            $data = [
            'isSaved' => true];
        }

        echo $this->templater->loadAdminPage('configuration.php', $data);

        $uploader = new ImageUploader();
        // $uploader->upload();
    }

    public function documentationHtml ()
    {
        $this->loadAssets();
        echo $this->templater->loadAdminPage('documentation.php');
    }

    public function myEntitiesHtml ()
    {
        $this->loadAssets();
        $entitiesCollection = new EntityCollection();

        $orderSense = "";
        $orderKey = "";
        if(isset($_GET['orderSense']) && isset($_GET['orderKey'])) {
            $orderSense = $_GET['orderSense'];
            $orderKey = $_GET['orderKey'];
        }

        $listRSS = $entitiesCollection->all($orderKey, $orderSense);

        echo $this->templater->loadAdminPage('rss-list.php', $listRSS);

        $messages = file_get_contents(plugin_dir_path( __FILE__ ).'messages.json');
        $json_a = json_decode($messages, true);
    }

    public function addEntitiesHtml ()
    {
        $this->loadAssets();
        $config = new ConfigOption();
        $configs = $config->getConfig();
        $data = new stdClass();

        foreach ($configs as $key => $value) {
            if($value->id == 1) {
                $data->category =  $value->default_value;
            }
            elseif ($value->id == 3) {
                $data->publish_type =  $value->default_value;
            }
            elseif ($value->id == 2) {
                $data->activate =  $value->default_value;
            }
        }

        echo $this->templater->loadAdminPage('rss-add.php', $data);
    }

    public function editHtml ()
    {
        $this->loadAssets();
        $entity = new Entity;
        $editingEntity = $entity->getById($_GET['id']);

        echo $this->templater->loadAdminPage('rss-edit.php', $editingEntity);
    }

    public function entityManager () 
    {
        $entityCollection = new EntityCollection();
        $entityOption = new ConfigOption();
        $config = $entityOption->getConfig();
        $linkToList = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-rss-list';

        // ACTION ENTITY : delete
        if(array_key_exists('sociallymap_deleteRSS', $_POST) && $_POST['sociallymap_deleteRSS']) {
            $idRemoving = $_POST['submit'];
            $entityCollection->deleteRowsByID($idRemoving);
        }

        // ACTION ENTITY : update
        if(array_key_exists('sociallymap_updateRSS', $_POST) && $_POST['sociallymap_updateRSS']) {
            if(!isset($_POST['sociallymap_activate'])) $_POST['sociallymap_activate'] = 0;

            $data = [
                'name'          => $_POST['sociallymap_label'],
                'category'      => $_POST['sociallymap_category'],
                'activate'      => $_POST['sociallymap_activate'],
                'sm_entity_id'  => $_POST['sociallymap_entityId'],
                'display_type'  => $_POST['sociallymap_display_type'],
                'publish_type'  => $_POST['sociallymap_publish_type'],
                'id'            => $_GET['id'],
            ];

            $entityCollection->update($data);
            wp_redirect($linkToList, 301 ); exit;  
        }


        // ACTION ENTITY : post
        if(array_key_exists('sociallymap_postRSS', $_POST) && $_POST['sociallymap_postRSS']) {
            if(!isset($_POST['sociallymap_activate'])) $_POST['sociallymap_activate'] = 0;

            $data = [
                'name'          => $_POST['sociallymap_name'],
                'category'      => $_POST['sociallymap_category'],
                'activate'      => $_POST['sociallymap_activate'],
                'sm_entity_id'  => $_POST['sociallymap_entityId'],
                'publish_type'  => $config[2]->default_value,
                'display_type'  => $config[1]->default_value,
            ];

            $entityCollection->add($data);
            wp_redirect($linkToList, 301 ); exit;  
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
    }

    public function loadAssets ($isFront = false) {
        // MODAL DISPLAY TYPE IS ON
        if($isFront) { 
            wp_enqueue_style('readmore', plugin_dir_url( __FILE__ ).'assets/styles/custom-readmore.css');
            wp_enqueue_style('fancybox', plugin_dir_url( __FILE__ ).'assets/styles/fancybox.css');

            wp_enqueue_script('jquery');
            wp_enqueue_script('fancy', plugin_dir_url( __FILE__ ).'assets/js/fancybox.js');
            wp_enqueue_script('modal-manager', plugin_dir_url( __FILE__ ).'assets/js/modal-manager.js');
        }
        else {
            wp_enqueue_style('back', plugin_dir_url( __FILE__ ).'assets/styles/back.css');
        }
    }

    public function githubConfiguration () {
        $config = array(
            'slug'               => plugin_basename(__FILE__), // this is the slug of your plugin
            'proper_folder_name' => 'wordpress-sociallymap', // this is the name of the folder your plugin lives in
            'api_url'            => 'https://api.github.com/repos/pgrmCreate/wordpress-sociallymap', // the GitHub API url of your GitHub repo
            'raw_url'            => 'https://raw.github.com/pgrmCreate/wordpress-sociallymap/github', // the GitHub raw url of your GitHub repo
            'github_url'         => 'https://github.com/pgrmCreate/wordpress-sociallymap', // the GitHub url of your GitHub repo
            'zip_url'            => 'https://github.com/pgrmCreate/wordpress-sociallymap/zipball/github', // the zip url of the GitHub repo
            'sslverify'          => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
            'requires'           => '4.3.1', // which version of WordPress does your plugin require?
            'tested'             => '4.3.1', // which version of WordPress is your plugin tested up to?
            'readme'             => 'README.md', // which file to use as the readme for the version number
            'access_token'       => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
        );

        new githubUpdater($config);
    }
}


new Sociallymap_Plugin();
