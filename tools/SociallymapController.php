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
            }
        }

        echo $this->templater->loadAdminPage('rss-add.php', $data);
    }

    public function listEntity()
    {
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
}
