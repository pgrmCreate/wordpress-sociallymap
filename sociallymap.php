<?php
/*
Plugin Name: Sociallymap
Plugin URI: http://www.sociallymap.com/
Description: A plugin that let the sociallymap users post on their blog from their mapping
Version: 1.0
Author: Alhena Conseil
Author URI: http://www.sociallymap.com/
License: Alhena © 2016
*/

require_once(plugin_dir_path(__FILE__).'includes/Templater.php');
require_once(plugin_dir_path(__FILE__).'includes/DbBuilder.php');
require_once(plugin_dir_path(__FILE__).'includes/Publisher.php');
require_once(plugin_dir_path(__FILE__).'includes/Requester.php');
require_once(plugin_dir_path(__FILE__).'includes/Logger.php');
require_once(plugin_dir_path(__FILE__).'includes/MockRequester.php');
require_once(plugin_dir_path(__FILE__).'includes/FileDownloader.php');
require_once(plugin_dir_path(__FILE__).'includes/MediaWordpressManager.php');
require_once(plugin_dir_path(__FILE__).'includes/SociallymapController.php');
require_once(plugin_dir_path(__FILE__).'includes/exception/fileDownloadException.php');
require_once(plugin_dir_path(__FILE__).'models/EntityCollection.php');
require_once(plugin_dir_path(__FILE__).'models/Entity.php');
require_once(plugin_dir_path(__FILE__).'models/Option.php');
require_once(plugin_dir_path(__FILE__).'models/ConfigOption.php');
require_once(plugin_dir_path(__FILE__).'models/Published.php');

class SociallymapPlugin
{
    private $wpdb;
    private $templater;
    private $controller;
    private $config_default_value;
    private $link_canononical;

    public function __construct()
    {
        global $wpdb;

        if (version_compare(phpversion(), '5.4', '<')) {
            echo('La version de PHP de votre hébergeur ne permet pas l\'utilisation de ce plugin');
        }

        $this->wpdb = $wpdb;
        $environment = '';

        $_ENV['URL_SOCIALLYMAP'] = [
            'prod'    => 'http://app.sociallymap.com',
            'staging' => 'http://app.sociallymap-staging.com',
            'dev'     => 'http://app.sociallymap.local',
        ];

        // DEV MOD : Active mock requester
        $_ENV['ENVIRONNEMENT'] = 'prod';

        $this->templater = new Templater();
        $this->controller = new SociallymapController();

        $this->link_canononical = '';

        if (array_key_exists('sociallymap_postEntity', $_POST)    ||
            array_key_exists('sociallymap_deleteEntity', $_POST)  ||
            array_key_exists('sociallymap_updateEntity', $_POST)  ||
            array_key_exists('sociallymap_updateConfig', $_POST) ) {
                add_action('admin_menu', [$this, 'entityManager']);
        }

        // todo comment routing system on all code
        add_action('init', [$this, 'rewriteInit']);
        add_action('template_redirect', [$this, 'redirectIntercept']);

        add_action('admin_menu', [$this, 'addAdminMenu']);

        add_filter('init', [$this, 'initialization']);

        register_activation_hook(__FILE__, [$this, 'activatePlugin']);
    }

    public function activatePlugin()
    {
        $builder = new DbBuilder();
        $builder->dbInitialisation();
    }

    public function rewriteCanonical($content)
    {
        $entityObject = new Entity();

        if (empty($content)) {
            Logger::alert('Rewrite link canonical : No content in entry');
            return false;
        }

        // Search entity and look canonical option
        $patternEntityId = '#data-entity-id="([0-9]+)"#';
        preg_match($patternEntityId, $content, $matches);
        if (isset($matches[1])) {
            $idSelect = $matches[1];
        } else {
            Logger::alert('Rewrite link canonical : No found entity');
            return false;
        }


        $patternUrl = '#data-article-url="(.+)"#';
        preg_match($patternUrl, $content, $matches);
        if (isset($matches[1])) {
            $entityUrl = $matches[1];
        } else {
            Logger::alert('Rewrite link canonical : No found url in content');
            return false;
        }

        $entityPicked = $entityObject->getById($idSelect);

        foreach ($entityPicked->options as $key => $value) {
            if ($value->options_id == '4') {
                $link_canonical = $value->value;
            }
        }

        // entity unknown
        if (empty($entityPicked)) {
            Logger::alert('Rewrite link canonical : Entity Unknown');
            return false;
        }

        if ($link_canonical) {
            // replace the default WordPress canonical URL function with your own
            $this->link_canononical = $entityUrl;
        }
        return $content;
    }

    public function initialization()
    {
        $this->loadAssets(true);

        remove_action('wp_head', 'rel_canonical');
        // add_action('wp_head', [$this, 'rewriteCanonical']);
        add_action('wp_head', [$this, 'customRelCanonical']);
        add_action('wp_head', [$this, 'noIndexRef']);

        if ($_ENV['ENVIRONNEMENT'] == 'dev') {
            $collector = new EntityCollection();
            $entity = $collector->getByEntityId('568ccccd7c5a00c6629e884f');

            if (!$entity) {
                Logger::error('DEV MODE : Entity cann\'t be load!');
            } else {
                $this->manageMessages($entity);
            }
        }
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

            Logger::info('Intercept message in plugin', esc_html(print_r($_POST, true)) );

            // We don't have the right parameters
            if (!isset($_POST['entityId']) || !isset($_POST['token'])) {
                header('HTTP/1.0 400 Bad Request');
                exit;
            }

            $collector = new EntityCollection();
            $_POST['entityId'] = esc_html($_POST['entityId']);
            $entity = $collector->getByEntityId($_POST['entityId']);


            // Context : Testing connection between sociallymap and wordpress plugin
            $_POST['token'] = esc_html($_POST['token']);
            if ($_POST['token'] == 'connection-test') {
                header('Content-Type: application/json');
                if (empty($entity)) {
                    header('HTTP/1.0 404 Not Found');
                    exit(json_encode([
                        'error' => 'entityId inconnu'
                    ]));
                } else {
                    // header('Content-Type: application/json');
                    header('HTTP/1.0 200 OK');
                    exit(json_encode([
                        'message' => 'ok'
                    ]));
                }
            }

            // This entity not exists
            if (empty($entity)) {
                header('HTTP/1.0 404 Not Found');
                exit;
            }

            // Try to retrieve the pending messages
            if ($this->manageMessages($entity) == false) {
                header('HTTP/1.0 502 Bad gateway');
                Logger::error('The plugin can\'t ping to sociallymap');
            } else {
                header('HTTP/1.0 200 OK');
                exit(json_encode([
                    'message' => 'ok'
                ]));
            }

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

    public function noIndexRef()
    {
        global $post;

        if (!is_single()) {
            return false;
        }

        $entityObject = new Entity();

        // get entity ID
        $pattern = '#data-entity-id="([0-9]+)"#';
        preg_match($pattern, $post->post_content, $matches);
        if (isset($matches[1])) {
            $idSelect = $matches[1];
        } else {
            Logger::alert('noIndex option : No found entity attribute in content');

            return false;
        }

        // id unknown
        if (empty($idSelect)) {
            Logger::alert('noIndex option : No found entity in bdd');
            return false;
        }

        $entityPicked = $entityObject->getById($idSelect);
        $noIndex = 0;
        $follow = 1;

        if ($entityPicked) {

            foreach ($entityPicked->options as $key => $value) {
                if ($value->options_id == '7') {
                    $noIndex = $value->value;
                }
                if ($value->options_id == '8') {
                    $follow = $value->value;
                }
            }

            // @todo manage conditions
            $contentArr = [];
            if ($noIndex) {
                $contentArr[] = 'noIndex';
            }
            if ($follow) {
                $contentArr[] = 'noFollow';
            }

            echo '<meta name="robots" content="' . implode(', ', $contentArr) . '">';

        }
    }

    public function customRelCanonical()
    {
        global $post;

        if (is_single() && $this->link_canononical != '') {
            $this->rewriteCanonical($post->post_content);
            echo '<link rel="canonical" href="'.$this->link_canononical.'" />';
        }
    }

    public function prePosting($content)
    {
        global $post;


        $entityObject = new Entity();
        $link_canonical = false;

        $pattern = '#data-entity-id="([0-9]+)"#';
        $returnSearch = preg_match($pattern, $content, $matches);

        if ($returnSearch != 1) {
            return $content;
        }

        if (isset($matches[1])) {
            $idSelect = $matches[1];
        } else {
            return $content;
        }

        // id unknown
        if (!isset($idSelect) || empty($idSelect)) {
            return $content;
        }

        $entityPicked = $entityObject->getById($idSelect);

        // entity unknown
        if (!isset($entityPicked) || empty($entityPicked)) {
            return $content;
        }

        foreach ($entityPicked->options as $key => $value) {
            if ($value->options_id == '2') {
                $display_type = $value->value;
            }

            if ($value->options_id == '4') {
                $link_canonical = $value->value;
            }

            if ($value->options_id == '8') {
                $link_canonical = $value->value;
            }

            if ($value->options_id == '9') {
                $morebalise = $value->value;
            }
        }

        if ($display_type == 'tab') {
            $content = preg_replace('#data-fancybox-type="iframe"#', '', $content);
        } elseif ($display_type == 'modal') {
            if (preg_match('#data-fancybox-type="iframe"#', $content) == 0) {
                $content = preg_replace(
                    '#<p><a class="sm-readmore#',
                    '<p><a data-fancybox-type="iframe" class="sm-readmore',
                    $content
                );
            }
        }

        if (isset($morebalise) && $morebalise == '1') {
            $content = preg_replace('#<p><a class="sm-readmore#', '<!--more--><p><a class="sm-readmore', $content);
        } else {
            $content = preg_replace('#<p><a class="sm-readmore#"', '<p><a class="sm-readmore', $content);
        }

        // $content = preg_replace('#data-display-type#', 'data-display-type="'.$display_type.'"', $content);

        return $content;
    }

    public function addAdminMenu()
    {
        add_menu_page(
            'Sociallymap publisher',
            'Sociallymap',
            'manage',
            'sociallymap-publisher',
            function () {
                $this->loadTemplate('documentation');
            },
            plugin_dir_url(__FILE__).'assets/images/icon.png'
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Mes entités',
            'Mes entités',
            'manage_options',
            'sociallymap-entity-list',
            function () {
                $this->loadTemplate('listEntities');
            }
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Ajouter une entité',
            'Ajouter une entité',
            'manage_options',
            'sociallymap-entity-add',
            function () {
                $this->loadTemplate('addEntity');
            }
        );

        add_submenu_page(
            'sociallymap-publisher',
            'Documentation',
            'Documentation',
            'manage_options',
            'sociallymap-documentation',
            function () {
                $this->loadTemplate('documentation');
            }
        );

        add_submenu_page(
            null,
            'edit entity',
            'Editer lien',
            'manage_options',
            'sociallymap-entity-edit',
            function () {
                $this->loadTemplate('editEntity');
            }
        );
    }

    public function manageMessages($entity)
    {
        $requester      = new Requester();
        $publisher      = new Publisher();
        $entityObject   = new Entity();
        $downloader     = new FileDownloader();
        $published      = new Published();
        $summary        = '';
        $title          = '';
        $readmoreLabel  = '';

        // get author id
        $author = $entity->author_id;

        // The entity is not active
        if (!$entity->activate) {
            return 0;
        }

        // Retrieve the entity categories
        $entityListCategory = [];
        $readmoreLabel = '';

        foreach ($entity->options as $key => $value) {
            if ($value->options_id == 1) {
                $entityListCategory[] = $value->value;
            }

            if ($value->options_id == 2) {
                $entityDisplayType = $value->value;
            }

            if ($value->options_id == 3) {
                $entityPublishType = $value->value;
            }

            if ($value->options_id == 5) {
                $entityImage = $value->value;
            }

            if ($value->options_id == 6) {
                $readmoreLabel = $value->value;
            }
        }

        // Try request to sociallymap on response
        try {
            if ($_ENV['ENVIRONNEMENT'] === 'dev') {
                $requester = new MockRequester();
                $jsonData  = $requester->getMessages();
            } else {
                $_POST['entityId'] = esc_html($_POST['entityId']);
                $_POST['token'] = esc_html($_POST['token']);
                $_POST['environment'] = esc_html($_POST['environment']);

                $jsonData = $requester->launch($_POST['entityId'], $_POST['token'], $_POST['environment']);
            }

            if (empty($jsonData)) {
                throw new Exception('No data returned from request', 1);
                exit();
            }

            Logger::messageReceive('See return data', $jsonData);

            foreach ($jsonData as $key => $value) {
                $summary = '';
                $contentArticle = '';
                $readmore = '';

                // Check Link object existing
                if (isset($value->link)) {
                    // Check if Title existing
                    if (!empty($value->link->title)) {
                        $title = $value->link->title;
                    }

                    if (!empty($value->link->summary)) {
                        $summary = $value->link->summary;
                    }

                    // Check if Link URL existing
                    if (!empty($value->link->url)) {
                        $readmoreLabel = stripslashes($readmoreLabel);

                        $readmore = $this->templater->loadReadMore(
                            $value->link->url,
                            $entityDisplayType,
                            $entity->id,
                            $readmoreLabel
                        );
                    } else {
                        Logger::alert('This article not contain url');
                    }
                }

                $contentArticle = $summary;
                // add readmore to content if $readmore is not empty
                if ($readmore != '') {
                    $contentArticle .= $readmore;
                }

                $imageTag = '';
                $imageSrc = '';

                // Check if article posted
                $canBePublish = true;
                $messageId = $value->guid;
                if ($published->isPublished($messageId)) {
                    $contextMessageId = '(id message='.$messageId.')';
                    Logger::alert('Message of sociallymap existing, so he is not publish', $contextMessageId);
                    $canBePublish = false;
                    continue;
                }

                $pathTempory = plugin_dir_path(__FILE__).'tmp/';
                // Check if Media object exist
                if (isset($value->media) && $value->media->type == 'photo') {
                    try {
                        $returnDownload = $downloader->download($value->media->url, $pathTempory);
                        $filename = $returnDownload['filename'];
                        $fileExtension = $returnDownload['extension'];
                        $mediaManager = new MediaWordpressManager();
                        $imageSrc = $mediaManager->integrateMediaToWordpress($filename, $fileExtension);

                    } catch (fileDownloadException $e) {
                        Logger::error('error download'.$e);
                    }

                    // WHEN NO ERROR : FORMAT
                    if (gettype($imageSrc) == 'string') {
                        $imageTag = '<img class="aligncenter" src="'.$imageSrc.'" alt="">';
                    } else {
                        $imageTag = '';
                    }
                } elseif (isset($value->link) && !empty($value->link->thumbnail) && $value->link->thumbnail != ' ') {
                    // Check if Image thumbnail existing
                    try {
                        $returnDownload = $downloader->download($value->link->thumbnail, $pathTempory);
                        $filename = $returnDownload['filename'];
                        $fileExtension = $returnDownload['extension'];
                    } catch (fileDownloadException $e) {
                        Logger::error('error download'.$e);
                    }

                    $mediaManager = new MediaWordpressManager();
                    $imageSrc = $mediaManager->integrateMediaToWordpress($filename, $fileExtension);

                    // Create the img tag
                    if (gettype($imageSrc) == 'string') {
                        $imageTag = '<img class="aligncenter" src="'.$imageSrc.'" alt="">';

                    } else {
                        $imageTag = '';
                    }
                }

                // check if video exist
                $downloadVideo = false;
                if (isset($value->media) && $value->media->type == 'video') {
                    $returnDownload = $downloader->download($value->media->url, $pathTempory);
                    $filename = $returnDownload['filename'];
                    $fileExtension = $returnDownload['extension'];
                    $mediaManager = new MediaWordpressManager();
                    $videoSrc = $mediaManager->integrateMediaToWordpress($filename, $fileExtension);

                    $mediaVideo = '<video class="sm-video-display" controls>
                    <source src="'.$videoSrc.'" type="video/mp4">
                    <div class="sm-video-nosupport"></div>
                    </video>';
                    $contentArticle .= $mediaVideo;
                    Logger::info('download VIDEO', $videoSrc);
                }

                // If imageTag is '' so is false else $isDownload is true
                $isDownloaded = ($imageTag !== '');


                // Attach image accordingly to options
                $imageAttachment = '';
                if ($isDownloaded) {
                    // Add image in the post content
                    if (in_array($entityImage, ['content', 'both'])) {
                        $contentArticle = $imageTag . $contentArticle;
                    }
                    // Add image as featured image
                    if (in_array($entityImage, ['thumbnail', 'both'])) {
                        $imageAttachment = $imageSrc;
                    }
                }

                // Publish the post
                $title = $value->content;
                if ($canBePublish == true) {
                        $dataPublish = [
                            $title,
                            $contentArticle,
                            $author,
                            $imageAttachment,
                            $entityListCategory,
                            $entityPublishType];

                        Logger::info('Try publish : ', $dataPublish);

                        $contentArticle = $this->prePosting($contentArticle);

                        $articlePublished = $publisher->publish(
                            $title,
                            $contentArticle,
                            $author,
                            $imageAttachment,
                            $entityListCategory,
                            $entityPublishType
                        );

                        if (!$articlePublished) {
                            // throw new Exception('Error from post publish', 1);
                            Logger::error('Error from post publish', [$title]);
                        } else {
                            $entityObject->updateHistoryPublisher($entity->id, $entity->counter);
                            // save published article
                            $published->add($messageId, $entity->id, $articlePublished);
                        }
                }
            }
        } catch (Exception $e) {
            Logger::alert('Error exeption', $e->getMessage());
        }
        return true;
    }

    public function loadTemplate($params)
    {
        $this->loadAssets();

        $this->controller->$params();
    }

    public function updatePosts()
    {
        $args = [
        'posts_per_page'   => -1,
        'offset'           => 0,
        'category'         => '',
        'category_name'    => '',
        'orderby'          => 'date',
        'order'            => 'DESC',
        'include'          => '',
        'exclude'          => '',
        'meta_key'         => '',
        'meta_value'       => '',
        'post_type'        => 'post',
        'post_mime_type'   => '',
        'post_parent'      => '',
        'author'           => '',
        'post_status'      => 'publish',
        'suppress_filters' => true,
        ];

        $posts_array = get_posts($args);

        foreach ($posts_array as $key => $value) {
            $newContent = $this->prePosting($value->post_content);
            $postUpdate = [
                'ID' => $value->ID,
                'post_content' => $newContent,
            ];

            wp_update_post($postUpdate);
        }
    }

    public function entityManager()
    {
        $entityCollection = new EntityCollection();

        // check http or https
        if (isset($_SERVER['HTTPS'])) {
            $linkToList = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-entity-list';
        } else {
            $linkToList = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=sociallymap-entity-list';
        }

        // ACTION ENTITY : delete
        if (array_key_exists('sociallymap_deleteEntity', $_POST) && $_POST['sociallymap_deleteEntity']) {
            $_POST['submit'] = esc_html($_POST['submit']);
            $idRemoving = $_POST['submit'];
            $entityCollection->deleteRowsByID($idRemoving);
        }

        // ACTION ENTITY : update
        if (array_key_exists('sociallymap_updateEntity', $_POST) && $_POST['sociallymap_updateEntity']) {
            if (isset($_POST['sociallymap_name']) && empty($_POST['sociallymap_name'])) {
                $isValid = false;
            }
            if (isset($_POST['sociallymap_entityId']) && empty($_POST['sociallymap_entityId'])) {
                $isValid = false;
            }

            if (!isset($_POST['sociallymap_activate'])) {
                $_POST['sociallymap_activate'] = 0;
            }
            if (!isset($_POST['sociallymap_category'])) {
                $_POST['sociallymap_category'] = [];
            }
            if (!isset($_POST['sociallymap_display_type'])) {
                $_POST['sociallymap_display_type'] = 'tab';
            }
            if (!isset($_POST['sociallymap_link_canonical'])) {
                $_POST['sociallymap_link_canonical'] = 0;
            }
            if (!isset($_POST['sociallymap_noIndex'])) {
                $_POST['sociallymap_noIndex'] = 0;
            }
            if (!isset($_POST['sociallymap_noFollow'])) {
                $_POST['sociallymap_noFollow'] = 0;
            }

            if (!isset($_POST['sociallymap_morebalise'])) {
                $_POST['sociallymap_morebalise'] = 0;
            }
            if (!isset($_POST['sociallymap_readmore'])) {
                $_POST['sociallymap_readmore'] = '';
            } else {
                $_POST['sociallymap_readmore'] = stripslashes($_POST['sociallymap_readmore']);
            }

            $data = [
                'name'           => esc_html($_POST['sociallymap_label']),
                'category'       => $_POST['sociallymap_category'],
                'activate'       => esc_html($_POST['sociallymap_activate']),
                'sm_entity_id'   => esc_html($_POST['sociallymap_entityId']),
                'display_type'   => esc_html($_POST['sociallymap_display_type']),
                'publish_type'   => esc_html($_POST['sociallymap_publish_type']),
                'link_canonical' => esc_html($_POST['sociallymap_link_canonical']),
                'noIndex'        => esc_html($_POST['sociallymap_noIndex']),
                'noFollow'       => esc_html($_POST['sociallymap_noFollow']),
                'image'          => esc_html($_POST['sociallymap_image']),
                'readmore'       => esc_html($_POST['sociallymap_readmore']),
                'morebalise'     => esc_html($_POST['sociallymap_morebalise']),
                'id'             => esc_html($_GET['id']),
            ];

            $entityCollection->update($data);
            $this->updatePosts();
            wp_redirect($linkToList, 301);
            exit;
        }


        // ACTION ENTITY : post
        if (array_key_exists('sociallymap_postEntity', $_POST) && $_POST['sociallymap_postEntity']) {
            $isValid = true;

            if (isset($_POST['sociallymap_name']) && empty($_POST['sociallymap_name'])) {
                $isValid = false;
            }
            if (isset($_POST['sociallymap_entityId']) && empty($_POST['sociallymap_entityId'])) {
                $isValid = false;
            }

            if (!isset($_POST['sociallymap_activate'])) {
                $_POST['sociallymap_activate'] = 0;
            }

            if (!isset($_POST['sociallymap_category'])) {
                $_POST['sociallymap_category'] = '';
            }
            if (!isset($_POST['sociallymap_display_type'])) {
                $_POST['sociallymap_display_type'] = 'tab';
            }
            if (!isset($_POST['sociallymap_link_canonical'])) {
                $_POST['sociallymap_link_canonical'] = 0;
            }
            if (!isset($_POST['sociallymap_noIndex'])) {
                $_POST['sociallymap_noIndex'] = 0;
            }
            if (!isset($_POST['sociallymap_noFollow'])) {
                $_POST['sociallymap_noFollow'] = 0;
            }
            if (!isset($_POST['sociallymap_readmore'])) {
                $_POST['sociallymap_readmore'] = '';
            }
            if (!isset($_POST['sociallymap_morebalise'])) {
                $_POST['sociallymap_morebalise'] = '';
            }

            if ($isValid == false) {
                $_POST['sociallymap_isNotValid'] = true;
                return false;
            }

            $data = [
                'name'           => esc_html($_POST['sociallymap_name']),
                'category'       => esc_html($_POST['sociallymap_category']),
                'activate'       => esc_html($_POST['sociallymap_activate']),
                'sm_entity_id'   => esc_html($_POST['sociallymap_entityId']),
                'publish_type'   => esc_html($_POST['sociallymap_publish_type']),
                'display_type'   => esc_html($_POST['sociallymap_display_type']),
                'link_canonical' => esc_html($_POST['sociallymap_link_canonical']),
                'noIndex'        => esc_html($_POST['sociallymap_noIndex']),
                'noFollow'       => esc_html($_POST['sociallymap_noFollow']),
                'readmore'       => esc_html($_POST['sociallymap_readmore']),
                'image'          => esc_html($_POST['sociallymap_image']),
                'morebalise'     => esc_html($_POST['sociallymap_morebalise']),
            ];

            $entityCollection->add($data);
            wp_redirect($linkToList, 301);
            exit;
        }
    }

    public function loadAssets($isFront = false)
    {
        // MODAL DISPLAY TYPE IS ON
        if ($isFront) {
            wp_enqueue_style('readmore', plugin_dir_url(__FILE__).'assets/css/custom-readmore.css');
            wp_enqueue_style('modalw-style', plugin_dir_url(__FILE__).'assets/modalw/modalw-style.css');

            wp_enqueue_script('jquery');
            wp_enqueue_script('modalw-script', plugin_dir_url(__FILE__).'assets/modalw/modal-windows.js');
        } else {
            wp_enqueue_style('back', plugin_dir_url(__FILE__).'assets/css/back.css');
        }
    }
}

register_activation_hook(__FILE__, ['SociallymapPlugin', 'install']);

new SociallymapPlugin();
