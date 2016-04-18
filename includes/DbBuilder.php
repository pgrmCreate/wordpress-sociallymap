<?php

class DbBuilder
{
    private $wpdb;
    private $tableOptions;
    private $tableEntityOptions;
    private $tableEntity;
    private $tablePublished;


    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->tableOptions = $this->wpdb->prefix .'sm_options';
        $this->tableEntityOptions = $this->wpdb->prefix .'sm_entity_options';
        $this->tableEntity = $this->wpdb->prefix .'sm_entities';
        $this->tablePublished = $this->wpdb->prefix .'sm_published';
    }

    public function dbInitialisation()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charsetCollate = $this->wpdb->get_charset_collate();

        if ($this->wpdb->get_var("SHOW TABLES LIKE '$this->tablePublished'") != $this->tablePublished) {
            $sql = "CREATE TABLE $this->tablePublished (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                entity_id varchar(255),
                message_id varchar(255),
                post_id varchar(255),
                UNIQUE KEY id (id)
                ) $charsetCollate;";

            dbDelta($sql);
        }

        if ($this->wpdb->get_var("SHOW TABLES LIKE '$this->tableEntity'") != $this->tableEntity) {
            $sql = "CREATE TABLE $this->tableEntity (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                sm_entity_id varchar(255),
                activate boolean,
                author_id varchar(255),
                name varchar(255),
                counter integer DEFAULT 0,
                last_published_message datetime,
                UNIQUE KEY id (id)
                ) $charsetCollate;";

            dbDelta($sql);
        }

        if ($this->wpdb->get_var("SHOW TABLES LIKE '$this->tableOptions'") != $this->tableOptions) {
            $sql = "CREATE TABLE $this->tableOptions (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                default_value varchar(255),
                label varchar(255),
                UNIQUE KEY id (id)
                ) $charsetCollate;";

            dbDelta($sql);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'category',
                'default_value' => 0,
            ], ['%s', '%d']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'display_type',
                'default_value' => 'tab',
            ], ['%s', '%s']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'publish_type',
                'default_value' => 'draft',
            ], ['%s', '%s']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'link_canonical',
                'default_value' => '1',
            ], ['%s', '%d']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'image',
                'default_value' => 'content',
            ], ['%s', '%s']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'readmore_label',
                'default_value' => 'lire la suite',
            ], ['%s', '%s']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'no index article',
                'default_value' => 0,
            ], ['%s', '%d']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'no folow',
                'default_value' => 0,
            ], ['%s', '%d']);

            $this->wpdb->insert($this->tableOptions, [
                'label' => 'balise more',
                'default_value' => 1,
            ], ['%s', '%d']);
        }

        if ($this->wpdb->get_var("SHOW TABLES LIKE '$this->tableEntityOptions'") != $this->tableEntityOptions) {
            $sql = "CREATE TABLE $this->tableEntityOptions (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                entity_id mediumint(9),
                options_id mediumint(9),
                value text,
                UNIQUE KEY id (id)
                ) $charsetCollate;";

            dbDelta($sql);
        }
    }

    public function destroyAll()
    {
        global $wpdb;

        $wpdb->query('DROP TABLE IF EXISTS '.$this->tableOptions);
        $wpdb->query('DROP TABLE IF EXISTS '.$this->tableEntityOptions);
        $wpdb->query('DROP TABLE IF EXISTS '.$this->tableEntity);
        $wpdb->query('DROP TABLE IF EXISTS '.$this->tablePublished);
    }
}
