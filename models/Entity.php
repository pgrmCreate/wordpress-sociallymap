<?php

class Entity
{
    private $table;

    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix.'sm_entities';
    }

    public function save($data)
    {
        global $wpdb;

        $wpdb->insert($this->table, $data);

        return $wpdb->insert_id;
    }

    public function getById($id, $orderKey = "", $orderSense = "")
    {
        global $wpdb;

        $entityRequest = $wpdb->prepare('SELECT * FROM '.$this->table.' WHERE id=%d', $id);
        $entity = $wpdb->get_row($entityRequest);
        $entity->options = new stdClass;

        $optionsRequest = 'SELECT options_id, value FROM '.$wpdb->prefix.'sm_entity_options WHERE entity_id = '.$id;
        $options = $wpdb->get_results($optionsRequest);

        $entity->options = $options;

        return $entity;
    }

    public function getByEntityId($entityId)
    {
        global $wpdb;

        $entityRequest = $wpdb->prepare('SELECT * FROM '.$this->table.' WHERE sm_entity_id=%s', $entityId);
        $entity = $wpdb->get_row($entityRequest);

        if (empty($entity)) {
            return false;
        }

        $optionsRequest = $wpdb->prepare('SELECT options_id, value FROM '.$wpdb->prefix.'sm_entity_options WHERE entity_id = %d', $entity->id);
        $options = $wpdb->get_results($optionsRequest);

        $entity->options = $options;

        return $entity;
    }

    public function deleteById($id)
    {
        global $wpdb;

        return $wpdb->delete($this->table, ['id' => $id], ['%d']);
    }

    public function update($data)
    {
        global $wpdb;

        if (!isset($data['id'])) {
            return false;
        }

        $wpdb->update(
            $this->table,
            [
                'name'         => $data['name'],    // string
                'activate'     => $data['activate'],    // string
                'sm_entity_id' => $data['sm_entity_id'],    // string
            ],
            [ 'ID' => $data['id'] ],
            [
               '%s',
               '%d'], // value1
            ['%d']
        );
    }

    public function updateHistoryPublisher($id, $counter)
    {
        global $wpdb;

        $counter++;

        $wpdb->update(
            $this->table,
            [
                'last_published_message'     => date('Y-m-d H:i:s'),    // string
               'counter'     => $counter,    // string
            ],
            [ 'ID' => $id ],
            ['%s', '%d'], // value1
            ['%d']
        );
    }
}
