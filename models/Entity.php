<?php

class Entity
{
	private $table;

	public function __construct () {
		global $wpdb;

		$this->table = $wpdb->prefix.'sm_entities';
	}

	public function save($data) 
	{
		global $wpdb;

		$wpdb->insert($this->table, $data);

		return $wpdb->insert_id;
	}

	public function getById ($id, $orderKey="",$orderSense="") {
		global $wpdb;

		$entityRequest = 'SELECT * FROM '.$this->table.' WHERE id='.$id;
		$entity = $wpdb->get_row($entityRequest);

		$optionsRequest = 'SELECT options_id, value FROM '.$wpdb->prefix.'sm_entity_options WHERE entity_id = '.$id;
		$options = $wpdb->get_results($optionsRequest);

		$entity->options = $options;

		return $entity;
	}

	public function deleteById($id) {
		global $wpdb;

		return $wpdb->delete($this->table, ['id' => $id], ['%d']);
	}

	public function update($data) {
		global $wpdb;

		$wpdb->update( 
			$this->table, [ 
				'name'     => $data['name'],	// string
				'activate' => $data['activate'],	// string
				'sm_entity_id' => $data['sm_entity_id'],	// string
			], 
			[ 'ID' => $data['id'] ], 
			['%s', '%d'],	// value1
			['%d']
		);
	}
}