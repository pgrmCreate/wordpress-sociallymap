<?php

class Publisher
{
    public function publish($title, $content, $author, $image, $category = 1, $publish_type = 'draft')
    {
        $listCats = [];
        if (is_array($category)) {
            foreach ($category as $key => $value) {
                $listCats[] = $value;
            }
        }

        $post = [
            'post_title' => $title,
            'post_content' => $content,
            'post_category' => $listCats,
            'post_status' => $publish_type,
            'post_author' => $author,
        ];

        //temporarily disable
        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');

        try {
            $newPostId = wp_insert_post($post);
        } catch (Exception $e) {
            logger::alert($e->getMessage());
            return false;
        }

        if ($newPostId == false) {
            return false;
        }

        // attach image to post if $image is not empty
        if ($image != '') {
            $filetype = wp_check_filetype(basename($image), null);
            $wp_upload_dir = wp_upload_dir();
            $attachment = [
                'guid'           => $wp_upload_dir['url'] . '/' . basename($image),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($image)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];
            $attach_id = wp_insert_attachment($attachment, $image, $attachment);
            $attach_data = wp_generate_attachment_metadata($attach_id, $image);
            wp_update_attachment_metadata($attach_id, $attach_data);
            set_post_thumbnail($newPostId, $attach_id);
        }

        //bring it back once you're done posting
        add_filter('content_save_pre', 'wp_filter_post_kses');
        add_filter('content_filtered_save_pre', 'wp_filter_post_kses');

        return $newPostId;
    }
}
