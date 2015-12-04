<?php

class Published
{
    private $table;

    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix.'sm_published';
    }

    public function all()
    {
        global $wpdb;

        $publishedRequest = 'SELECT * FROM '.$this->table;
        $articlesPublished = $wpdb->get_results($entitiesRequest);

        if (empty($articlesPublished)) {
            return false;
        } else {
            return $listRSS;
        }
    }

    public function isPublished($idMessage)
    {
        global $wpdb;

        $publishedRequest = 'SELECT * FROM '.$this->table.' WHERE message_id='.$idMessage;
        $articlesPublished = $wpdb->get_results($entitiesRequest);

        if (empty($articlesPublished)) {
            return false;
        } else {
            return true;
        }
    }

    public function add($messageId, $entityId, $postId)
    {
        global $wpdb;

        $data = [
            'entity_id' => $entityId,
            'message_id' => $messageId,
            'post_id' => $postId,
        ];

        $wpdb->insert($this->table, $data);

        return $wpdb->insert_id;
    }
}
