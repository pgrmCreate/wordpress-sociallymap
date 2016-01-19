<?php

class SociallymapController
{
    private $templater;

    public function __construct()
    {
        $this->templater = new Templater();
    }

    public function configuration()
    {
        $data = [];

        if (array_key_exists('sociallymap_updateConfig', $_POST)) {
            $data = [
            'isSaved' => true];
        }

        echo $this->templater->loadAdminPage('configuration.php', $data);
    }

    public function documentation()
    {
        $data = [];

        echo $this->templater->loadAdminPage('documentation.php', $data);
    }

    public function editEntity()
    {
        $entity = new Entity();
        $editingEntity = $entity->getById($_GET['id']);

        $categoryList = [];
        $publish_type = 'draft';
        $display_type = 'modal';
        $link_canonical = '1';

        foreach ($editingEntity->options as $key => $value) {
            switch ($value->options_id) {
                case '1':
                    $categoryList[] = esc_html($value->value);
                    break;

                case '2':
                    $display_type = esc_html($value->value);
                    break;

                case '3':
                    $publish_type = esc_html($value->value);
                    break;

                case '4':
                    $link_canonical = esc_html($value->value);
                    break;

                case '5':
                    $image = esc_html($value->value);
                    break;

                case '6':
                    $readmore = esc_html($value->value);
                    break;

                case '7':
                    $noIndex = esc_html($value->value);
                    break;

                case '8':
                    $noFollow = esc_html($value->value);
                    break;

                case '9':
                    $morebalise = esc_html($value->value);
                    break;

                default:
                    # code...
                    break;
            }
        }

        $editingEntity->options = new stdClass();
        $editingEntity->options->category = $categoryList;
        $editingEntity->options->publish_type = $publish_type;
        $editingEntity->options->display_type = $display_type;
        $editingEntity->options->link_canonical = $link_canonical;
        $editingEntity->options->image = $image;
        $editingEntity->options->noIndex = $noIndex;
        $editingEntity->options->noFollow = $noFollow;
        $editingEntity->options->readmore = $readmore;
        $editingEntity->options->morebalise = $morebalise;


        $sendItem['editingEntity'] = $editingEntity;


        echo $this->templater->loadAdminPage('entity-edit.php', $sendItem);
    }

    public function addEntity()
    {
        $data = new stdClass();

        echo $this->templater->loadAdminPage('entity-add.php', $data);
    }

    public function listEntities()
    {
        $entitiesCollection = new EntityCollection();

        $orderSense = "";
        $orderKey = "";
        if (isset($_GET['orderSense']) && isset($_GET['orderKey'])) {
            $orderSense = $_GET['orderSense'];
            $orderKey   = $_GET['orderKey'];
        }

        $listEntities = $entitiesCollection->all($orderKey, $orderSense);

        echo $this->templater->loadAdminPage('entity-list.php', $listEntities);
    }
}
