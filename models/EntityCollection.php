<?php

class EntityCollection
{
	private $table_entity;
	private $table_options;
	private $table_options_list;
	private $default_value;

	function __construct() {
		global $wpdb;
		$this->table_entity       = $wpdb->prefix.'sm_entities';
		$this->table_options      = $wpdb->prefix.'sm_entity_options';
		$this->table_options_list = $wpdb->prefix.'sm_options';

		$this->default_value = $wpdb->get_results("SELECT * FROM $this->table_options_list");
	}

	public function add($data) {
		global $wpdb;
		$entity = new Entity();
		$option = new Option();

		$dataEntity = [
			'activate'     => rand(0, 1),
			'sm_entity_id' => 0,
			'author_id'    => wp_get_current_user()->ID,
			'name'         => $data['name'],
		];
		$entityID = $entity->save($dataEntity);

		$dataOption = [
			'option_id'  => 1,
			'value' => $data['category']
		];
		$option->save($dataOption, $entityID);

		$dataOption = [
			'option_id'  => 2,
			'value' => $data['modal_mobile']
		];
		$option->save($dataOption, $entityID);

		$dataOption = [
			'option_id'  => 3,
			'value' => $data['modal_desktop']
		];
		$option->save($dataOption, $entityID);
	}

	public function update($data) {
		global $wpdb;
		$entity = new Entity();
		$option = new Option();

			$dataEntity = [
			'id'        => $data['id'],
			'name'      => $data['name'],
			'activate'  => $data['activate'],
			];
		$entity->update($dataEntity);

		$optionsEntity = [
			'idSource'      => $data['id'],
			'category'      => $data['category'],
			'modal_mobile'  => $data['modal_mobile'],
			'modal_desktop' => $data['modal_desktop'],
		];
		$option->update($optionsEntity);
	}

	public function all() {
		global $wpdb;
		$entitiesRequest = 'SELECT * FROM '.$this->table_entity;
		
		$entities = $wpdb->get_results($entitiesRequest);

		return $entities;
	}

	public function deleteRowsByID ($id) {
		global $wpdb;

		$entity = new Entity();
		$option = new Option();

		$entity->deleteById($id);
		$option->deleteByOwnerId($id);

		return ($entity && $option);
	}
}