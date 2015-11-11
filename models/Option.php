<?php

class Option
{
	private $table;
	private $attributesTable;

	public function __construct () {
		global $wpdb;

		$this->table = $wpdb->prefix.'sm_entity_options';
	}

	public function getById ($id) {
		global $wpdb;

		$entityRequest = 'SELECT * FROM '.$this->table.' WHERE id='.$id;
	}

	public function deleteById($id) {
		global $wpdb;

		return $wpdb->delete($this->table, ['id' => $id], ['%d']);
	}

	// Destroy all tuples from parent entity
	public function deleteByOwnerId($id) {
		global $wpdb;

		return $wpdb->delete($this->table, ['entity_id' => $id], ['%d']);
	}

	public function save($data, $idSource) {
		global $wpdb;

		$wpdb->insert($this->table, [
			'entity_id'  => $idSource,
			'options_id' => $data['option_id'],
			'value'      => $data['value'],
			], ['%d', '%d', '%d']);
	}

	public function update($data) {
		global $wpdb;

		// UPDATE CATEGORY
		if(isset($data['category'])) {
			$wpdb->update( 
				$this->table, [ 
					'value' => $data['category'],	// string
				], [ 
					'entity_id' => $data['idSource'],
					'options_id' => 1
				], ['%d'],	// value1
				['%d']
			);
		}

		// UPDATE ISMODAL [MOBILE]
		if(isset($data['modal_mobile'])) {
			$wpdb->update( 
				$this->table, [ 
					'value' => $data['modal_mobile'],	// string
				], [ 
					'entity_id' => $data['idSource'],
					'options_id' => 2
				], ['%d'],	// value1
				['%d']
			);
		}

		// UPDATE ISMODAL [DESKTOP]
		if(isset($data['modal_desktop'])) {
			$wpdb->update( 
				$this->table, [ 
					'value' => $data['modal_desktop'],	// string
				], [ 
					'entity_id' => $data['idSource'],
					'options_id' => 3
				], ['%d'],	// value1
				['%d']
			);
		}
	}

}