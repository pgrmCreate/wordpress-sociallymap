<?php

class ConfigOption
{
    private $table;
    private $currentConfig;

    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix.'sm_options';
    }

    public function getConfig()
    {
        global $wpdb;

        $this->currentConfig = $wpdb->get_results('SELECT * FROM '.$this->table);

        return $this->currentConfig;
    }

    public function save($data)
    {
        global $wpdb;

        foreach ($data as $key => $value) {
            $wpdb->update(
                $this->table,
                [
                    'default_value' => $value,  // string
                ],
                [
                    'id' => $key
                ]
            );
        }

    }
}
