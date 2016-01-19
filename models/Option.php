<?php

class Option
{
    private $table;

    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix.'sm_entity_options';
    }

    public function getById($id)
    {
        global $wpdb;

        $entityRequest = $wpdb->prepare('SELECT * FROM '.$this->table.' WHERE id=%s', $id);
        $options = $wpdb->get_results($entityRequest);

        return $options;
    }

    public function getByEntityId($id)
    {
        global $wpdb;

        $entityRequest = $wpdb->prepare('SELECT * FROM '.$this->table.' WHERE entity_id=%s', $id);
        $options = $wpdb->get_results($entityRequest);

        return $options;
    }

    public function deleteById($id)
    {
        global $wpdb;

        return $wpdb->delete($this->table, ['id' => $id], ['%d']);
    }

    // Destroy all tuples from parent entity
    public function deleteByOwnerId($id)
    {
        global $wpdb;

        return $wpdb->delete($this->table, ['entity_id' => $id], ['%d']);
    }

    public function save($data, $idSource)
    {
        global $wpdb;

        $wpdb->insert($this->table, [
            'entity_id'  => $idSource,
            'options_id' => $data['option_id'],
            'value'      => $data['value'],
            ]);
    }

    public function update($data)
    {
        global $wpdb;


        // UPDATE CATEGORY
        if (isset($data['category'])) {
            $currentCatsRequest = $wpdb->prepare('
                SELECT value FROM '.$this->table.'
                WHERE entity_id = %s
                AND options_id = 1', $data['idSource']);
            $options = $wpdb->get_results($currentCatsRequest);

            $optCats = [];
            foreach ($options as $key => $value) {
                $optCats[] = $value->value;
            }

            if ($optCats != $data['category']) {
                $wpdb->delete($this->table, [
                    'entity_id'     => $data['idSource'],
                    'options_id'     => 1]);

                foreach ($data['category'] as $key => $value) {
                    $wpdb->insert($this->table, [
                        'entity_id'  => $data['idSource'],
                        'options_id' => 1,
                        'value'      => $value,
                    ]);
                }
            }
        }

        // UPDATE WINDOWS
        if (isset($data['display_type'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['display_type'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 2
                ]
            );
        }

        // UPDATE PUBLISH MODE
        if (isset($data['publish_type'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['publish_type'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 3
                ]
            );
        }

        // UPDATE LINK CANONICAL
        if (isset($data['link_canonical'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['link_canonical'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 4
                ]
            );
        }


        // UPDATE IMAGE ATTACHMENT
        if (isset($data['image'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['image'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 5
                ]
            );
        }

        // UPDATE IMAGE ATTACHMENT
        if (isset($data['readmore'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['readmore'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 6
                ]
            );
        }

        // UPDATE LINK CANONICAL
        if (isset($data['noIndex'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['noIndex'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 7
                ]
            );
        }

        // UPDATE LINK NOFOLOW
        if (isset($data['noFollow'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['noFollow'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 8
                ]
            );
        }

        // UPDATE BALISE READMORE
        if (isset($data['morebalise'])) {
            $wpdb->update(
                $this->table,
                [
                    'value' => $data['morebalise'],    // string
                ],
                [
                    'entity_id' => $data['idSource'],
                    'options_id' => 9
                ]
            );
        }
    }
}
