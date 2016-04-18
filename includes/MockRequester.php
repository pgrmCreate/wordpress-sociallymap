<?php

class MockRequester
{
    public function __construct()
    {

    }

    public function getMessages()
    {
        $returnFile = file_get_contents(plugin_dir_path(__FILE__).'../messages.json');
        $returnFile = json_decode($returnFile);

        return $returnFile;
    }
}
