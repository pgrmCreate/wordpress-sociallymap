<?php
/*
Plugin Name: Sociallymap
Plugin URI: https://github.com/alhenaconseil/wordpress-sociallymap
Description: A plugin that let the sociallymap users post on their blog from their mapping
Version: 1.0
Author: Sociallymap
Author URI: http://www.sociallymap.com/
License: MIT
*/

require_once(plugin_dir_path(__FILE__).'tools/Templater.php');
require_once(plugin_dir_path(__FILE__).'tools/Db-builder.php');
require_once(plugin_dir_path(__FILE__).'tools/Publisher.php');
require_once(plugin_dir_path(__FILE__).'tools/Requester.php');
require_once(plugin_dir_path(__FILE__).'tools/ImageUploader.php');
require_once(plugin_dir_path(__FILE__).'tools/GithubUpdater.php');
require_once(plugin_dir_path(__FILE__).'models/EntityCollection.php');
require_once(plugin_dir_path(__FILE__).'models/Entity.php');
require_once(plugin_dir_path(__FILE__).'models/Option.php');
require_once(plugin_dir_path(__FILE__).'models/ConfigOption.php');

class SociallymapPlugin
{
    private $wpdb;
    private $templater;
    private $config_default_value;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $_ENV["URL_SOCIALLYMAP"] = "http://app.sociallymap-staging.com";

        $this->templater = new Templater();

        $configsOption = new ConfigOption;
        $this->config_default_value = $configsOption->getConfig();


        $builder = new DbBuilder();
        $builder->dbInitialisation();

        if (array_key_exists('sociallymap_postRSS', $_POST)    ||
            array_key_exists('sociallymap_deleteRSS', $_POST)  ||
            array_key_exists('sociallymap_updateRSS', $_POST)  ||
            array_key_exists('sociallymap_updateConfig', $_POST) ) {
                add_action('admin_menu', [$this, 'entityManager']);
        }

        // todo comment routing system on all code
        add_action('init', [$this, 'rewriteInit']);
        add_action('template_redirect', [$this, 'redirectIntercept']);

        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_menu', [$this, 'githubConfiguration']);
        add_filter('the_content', [$this, "postFooter"]);
        add_filter('init', [$this, "initialisation"]);
    }

    public function initialisation()
    {
        $this->loadAssets(true);
    }

    public static function install()
    {
        global $wp_rewrite;

        self::addRewriteRules();

        $wp_rewrite->flush_rules();
    }

    public function redirectIntercept()
    {
        global $wp_query;

        if ($wp_query->get('sociallymap-plugin')) {
            error_log('Ping received: '.print_r($_POST, true));

            // We don't have the right parameters
            if (!isset($_POST['entityId']) || !isset($_POST['token'])) {
                header("HTTP/1.0 400 Bad Request");
                exit;
            }

            $collector = new EntityCollection();
            $entity = $collector->getByEntityId($_POST['entityId']);

            // This entity not exists
            if (empty($entity)) {
                header("HTTP/1.0 404 Not Found");
                exit;
            }

            // Try to retrieve the pending messages
            if ($this->manageMessages($entity) == false) {
                header("HTTP/1.0 502 Bad manage data");
                exit;
            }
            exit;
        }
    }

    public function rewriteInit()
    {
        add_rewrite_tag('%sociallymap-plugin%', '1');
        $this->addRewriteRules();
    }

    public static function addRewriteRules()
    {
        add_rewrite_rule('sociallymap', 'index.php?sociallymap-plugin=1', 'top');
    }

    public function postFooter($content)
    {
        global $post;
        $config = new ConfigOption();
        $configs = $config->getConfig();

        if ($configs[1]->default_value == "tab") {
            $footer = "<p data-hidden-display>tab</p>";
        } else {
            $footer = "<p data-hidden-display>modal</p>";
        }

        if ($footer) {
            return $content . $footer;
        }
        return $content;
    }

    public function addAdminMenu()
    {
        add_menu_page(
            'Sociallymap publisher',
            'Sociallymap',
            'manage',
            'sociallymap-publisher',
            [$this, 'documentation_html'],
            plugin_dir_url(__FILE__).'assets/images/logo.png'
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Mes entités',
            'Mes entités',
            'manage_options',
            'sociallymap-rss-list',
            [$this, 'myEntitiesHtml']
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Ajouter une entité',
            'Ajouter une entité',
            'manage_options',
            'sociallymap-rss-add',
            [$this, 'addEntitiesHtml']
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Configuration',
            'Configuration',
            'manage_options',
            'sociallymap-configuration',
            [$this, 'configurationHtml']
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Documentation',
            'Documentation',
            'manage_options',
            'sociallymap-documentation',
            [$this,
            'documentationHtml']
        );

        add_submenu_page(
            null,
            'edit entity',
            'Editer lien',
            'manage_options',
            'sociallymap-rss-edit',
            [$this, 'editHtml']
        );
    }

    public function manageMessages($entity)
    {
        $requester    = new Requester();
        $publisher    = new Publisher();
        $config       = new ConfigOption();
        $entityObject = new Entity();

        $configs = $config->getConfig();

        // The entity is not active
        if (!$entity->activate) {
            exit;
        }

        // Retrieve the entity categories
        $entity_list_category = [];
        foreach ($entity->options as $key => $value) {
            if ($value->options_id == 1) {
                $entity_list_category[] = $value->value;
            }

            if ($value->options_id == 3) {
                $entity_publish_type = $value->value;
            }
        }

        try {
            $jsonData = $requester->launch($_POST['entityId'], $_POST['token']);

            $baseReadMore = $this->templater->loadReadMore();

            foreach ($jsonData as $key => $value) {
                if (empty($value->linkTitle) ||
                    empty($value->linkThumbnail) ||
                    empty($value->$value->linkSummary) ||
                    empty($value->$value->linkUrl)) {
                    throw new Exception('Base pattern of message contain empty value', 1);
                }

                $title = $value->linkTitle;
                $uploader = new ImageUploader();

                $readmore = $this->templater->formatReadMoreUrl($baseReadMore, $value->linkUrl);

                $imagePost = $uploader->tryUploadPost($value->linkThumbnail, $value->media, $value->mediaType);
                $imagePost = substr($imagePost, 0, 5).'class="aligncenter"'.substr($imagePost, 5);
                
                if (empty($imagePost) || gettype($imagePost) != "string") {
                    throw new Exception('Error from uploading image posting', 1);
                }
                $contentArticle = '<p>'.$imagePost.'<br>'.$value->linkSummary.'</p>'.$readmore;
                $entityObject->updateHistoryPublisher($entityExisting->id, $entityExisting->counter);

                if (!$publisher->publish($title, $contentArticle, $entity_list_category, $entity_publish_type)) {
                    throw new Exception('Error from post publish', 1);
                }
            }
        } catch (Exception $e) {
            error_log('Sociallymap: Error data loading');
            exit;
        }
    }

    public function configurationHtml()
    {
        $this->loadAssets();
        $data = [];
        if (array_key_exists('sociallymap_updateConfig', $_POST)) {
            $data = [
            'isSaved' => true];
        }

        echo $this->templater->loadAdminPage('configuration.php', $data);

        $uploader = new ImageUploader();
        // $uploader->upload();
    }

    public function documentationHtml()
    {
        $this->loadAssets();
        echo $this->templater->loadAdminPage('documentation.php');
    }

    public function myEntitiesHtml()
    {
        $this->loadAssets();
        $entitiesCollection = new EntityCollection();

        $orderSense = "";
        $orderKey = "";
        if (isset($_GET['orderSense']) && isset($_GET['orderKey'])) {
            $orderSense = $_GET['orderSense'];
            $orderKey = $_GET['orderKey'];
        }

        $listRSS = $entitiesCollection->all($orderKey, $orderSense);

        echo $this->templater->loadAdminPage('rss-list.php', $listRSS);
    }

    public function addEntitiesHtml()
    {
        $this->loadAssets();
        $config = new ConfigOption();
        $configs = $config->getConfig();
        $data = new stdClass();

        foreach ($configs as $key => $value) {
            if ($value->id == 1) {
                $data->category = $value->default_value;
            } elseif ($value->id == 3) {
                $data->publish_type =  $value->default_value;
            } elseif ($value->id == 2) {
                $data->activate =  $value->default_value;
            }
        }

        echo $this->templater->loadAdminPage('rss-add.php', $data);
    }

    public function editHtml()
    {
        $this->loadAssets();
        $entity = new Entity;
        $editingEntity = $entity->getById($_GET['id']);

        $categoryList = [];
        $publish_type = "draft";
        foreach ($editingEntity->options as $key => $value) {
            if ($value->options_id == '1') {
                $categoryList[] = $value->value;
            }
            if ($value->options_id == '3') {
                $publish_type = $value->value;
            }
        }

        $editingEntity->options = new stdClass;
        $editingEntity->options->category = $categoryList;
        $editingEntity->options->publish_type = $publish_type;

        $sendItem['editingEntity'] = $editingEntity;

        echo $this->templater->loadAdminPage('rss-edit.php', $sendItem);
    }

    public function entityManager()
    {
        $entityCollection = new EntityCollection();
        $entityOption = new ConfigOption();
        $config = $entityOption->getConfig();
        $linkToList = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-rss-list';

        // ACTION ENTITY : delete
        if (array_key_exists('sociallymap_deleteRSS', $_POST) && $_POST['sociallymap_deleteRSS']) {
            $idRemoving = $_POST['submit'];
            $entityCollection->deleteRowsByID($idRemoving);
        }

        // ACTION ENTITY : update
        if (array_key_exists('sociallymap_updateRSS', $_POST) && $_POST['sociallymap_updateRSS']) {
            if (!isset($_POST['sociallymap_activate'])) {
                $_POST['sociallymap_activate'] = 0;
            }
            if (!isset($_POST['sociallymap_category'])) {
                $_POST['sociallymap_category'] = [];
            }

            $data = [
                'name'                   => $_POST['sociallymap_label'],
                'category'               => $_POST['sociallymap_category'],
                'activate'               => $_POST['sociallymap_activate'],
                'sm_entity_id'           => $_POST['sociallymap_entityId'],
                'display_type'           => $_POST['sociallymap_display_type'],
                'publish_type'           => $_POST['sociallymap_publish_type'],
                'id'                     => $_GET['id'],
            ];

            $entityCollection->update($data);
            wp_redirect($linkToList, 301);
            exit;
        }


        // ACTION ENTITY : post
        if (array_key_exists('sociallymap_postRSS', $_POST) && $_POST['sociallymap_postRSS']) {
            if (!isset($_POST['sociallymap_activate'])) {
                $_POST['sociallymap_activate'] = 0;
            }

            $data = [
                'name'          => $_POST['sociallymap_name'],
                'category'      => $_POST['sociallymap_category'],
                'activate'      => $_POST['sociallymap_activate'],
                'sm_entity_id'  => $_POST['sociallymap_entityId'],
                'publish_type'  => $config[2]->default_value,
                'display_type'  => $config[1]->default_value,
            ];

            $entityCollection->add($data);
            wp_redirect($linkToList, 301);
            exit;
        }

        // ACTION CONFIG : update
        if (array_key_exists('sociallymap_updateConfig', $_POST) && $_POST['sociallymap_updateConfig']) {
            $data = [
                1 => $_POST['sociallymap_category'],
                2 => $_POST['sociallymap_display_type'],
                3 => $_POST['sociallymap_publish_type'],
            ];

            $currentConfig = new ConfigOption;
            $currentConfig->save($data);
        }
    }

    public function loadAssets($isFront = false)
    {
        // MODAL DISPLAY TYPE IS ON
        if ($isFront) {
            wp_enqueue_style('readmore', plugin_dir_url(__FILE__).'assets/styles/custom-readmore.css');
            wp_enqueue_style('fancybox', plugin_dir_url(__FILE__).'assets/styles/fancybox.css');

            wp_enqueue_script('jquery');
            wp_enqueue_script('fancy', plugin_dir_url(__FILE__).'assets/js/fancybox.js');
            wp_enqueue_script('modal-manager', plugin_dir_url(__FILE__).'assets/js/modal-manager.js');
        } else {
            wp_enqueue_style('back', plugin_dir_url(__FILE__).'assets/styles/back.css');
        }
    }

    public function githubConfiguration()
    {
        $config = [
            // this is the slug of your plugin
            'slug'               => plugin_basename(__FILE__),
            // this is the name of the folder your plugin lives in
            'proper_folder_name' => 'wordpress-sociallymap',
            // the GitHub API url of your GitHub repo
            'api_url'            => 'https://api.github.com/repos/pgrmCreate/wordpress-sociallymap',
            // the GitHub raw url of your GitHub repo
            'raw_url'            => 'https://raw.github.com/pgrmCreate/wordpress-sociallymap/github',
            // the GitHub url of your GitHub repo
            'github_url'         => 'https://github.com/pgrmCreate/wordpress-sociallymap',
            // the zip url of the GitHub repo
            'zip_url'            => 'https://github.com/pgrmCreate/wordpress-sociallymap/zipball/github',
            // whether WP should check the validity of the SSL cert when getting an update,
            // see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and
            // https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
            'sslverify'          => true,
            // which version of WordPress does your plugin require?
            'requires'           => '4.3.1',
            // which version of WordPress is your plugin tested up to?
            'tested'             => '4.3.1',
            // which file to use as the readme for the version number
            'readme'             => 'README.md',
            // Access private repositories by authorizing under Appearance > GitHub Updates
            // when this example plugin is installed
            'access_token'       => '',
        ];

        new githubUpdater($config);
    }
}

register_activation_hook(__FILE__, ['SociallymapPlugin', 'install']);

new SociallymapPlugin();
