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
        $entity = new Entity;
        $editingEntity = $entity->getById($_GET['id']);

        $categoryList = [];
        $publish_type = "draft";
        $display_type = "modal";
        $link_canonical = "1";
        foreach ($editingEntity->options as $key => $value) {
            if ($value->options_id == '1') {
                $categoryList[] = $value->value;
            }
            if ($value->options_id == '2') {
                $display_type = $value->value;
            }
            if ($value->options_id == '3') {
                $publish_type = $value->value;
            }
            if ($value->options_id == '4') {
                $link_canonical = $value->value;
            }
            if ($value->options_id == '5') {
                $image = $value->value;
            }
            if ($value->options_id == '7') {
                $noindex = $value->value;
            }
        }

        $editingEntity->options = new stdClass;
        $editingEntity->options->category = $categoryList;
        $editingEntity->options->publish_type = $publish_type;
        $editingEntity->options->display_type = $display_type;
        $editingEntity->options->link_canonical = $link_canonical;
        $editingEntity->options->image = $image;

        $sendItem['editingEntity'] = $editingEntity;

        echo $this->templater->loadAdminPage('entity-edit.php', $sendItem);
    }

    public function addEntity()
    {
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
            } elseif ($value->id == 5) {
                $data->image =  $value->default_value;
            } elseif ($value->id == 7) {
                $data->noindex =  $value->default_value;
            }
        }

        echo $this->templater->loadAdminPage('entity-add.php', $data);
    }

    public function listEntities()
    {
        $entitiesCollection = new EntityCollection();

        $orderSense = "";
        $orderKey = "";
        if (isset($_GET['orderSense']) && isset($_GET['orderKey'])) {
            $orderSense = $_GET['orderSense'];
            $orderKey = $_GET['orderKey'];
        }

        $listRSS = $entitiesCollection->all($orderKey, $orderSense);

        echo $this->templater->loadAdminPage('entity-list.php', $listRSS);
    }
}
