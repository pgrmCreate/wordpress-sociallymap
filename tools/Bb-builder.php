<?php
	class DbBuilder {
		private $wpdb;
		private $table_options;
    	private $table_entity_options;
    	private $table_entity;


		public function __construct () {
			global $wpdb;
        	$this->wpdb = $wpdb;

			$this->table_options = $this->wpdb->prefix . "sm_options"; 
        	$this->table_entity_options = $this->wpdb->prefix . "sm_entity_options"; 
        	$this->table_entity = $this->wpdb->prefix . "sm_entities"; 
		}

		public function dbInitialisation() {
		
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	        $charset_collate = $this->wpdb->get_charset_collate();

	        if($this->wpdb->get_var("SHOW TABLES LIKE '$this->table_entity'") != $this->table_entity) {
	            $sql = "CREATE TABLE $this->table_entity (
	              id mediumint(9) NOT NULL AUTO_INCREMENT,
	              sm_entity_id varchar(255),
	              activate boolean,
	              author_id varchar(255),
	              name varchar(255),
	              last_published_message datetime,
	              UNIQUE KEY id (id)
	            ) $charset_collate;";

	            dbDelta($sql);
	        }

	        if($this->wpdb->get_var("SHOW TABLES LIKE '$this->table_options'") != $this->table_options) {
	            $sql = "CREATE TABLE $this->table_options (
	              id mediumint(9) NOT NULL AUTO_INCREMENT,
	              default_value varchar(255),
	              label varchar(255),
	              UNIQUE KEY id (id)
	            ) $charset_collate;";

	            dbDelta($sql);
	            
	            $this->wpdb->insert($this->table_options,[
	            'label' => 'category',
	            'default_value' => 0,
	            ], ['%s', '%d']);

	           	$this->wpdb->insert($this->table_options,[
	            'label' => 'display_type',
	            'default_value' => 'tab',
	            ], ['%s', '%s']);

	           	$this->wpdb->insert($this->table_options,[
	            'label' => 'publish_type',
	            'default_value' => 'draft',
	            ], ['%s', '%s']);
	        }

	        if($this->wpdb->get_var("SHOW TABLES LIKE '$this->table_entity_options'") != $this->table_entity_options) {
	            $sql = "CREATE TABLE $this->table_entity_options (
	              id mediumint(9) NOT NULL AUTO_INCREMENT,
	              entity_id mediumint(9),
	              options_id mediumint(9),
	              value text,
	              UNIQUE KEY id (id)
	            ) $charset_collate;";
	    
	            dbDelta($sql);
	        } 
		}

		public function destroyAll() {
			echo("<br>Tout a été détruit...................");
		}
	}