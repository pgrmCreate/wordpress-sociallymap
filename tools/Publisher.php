<?php

class Publisher
{
    public function publish($title, $content, $category = 1, $publish_type = 'draft')
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
            'post_author' => get_current_user_id(),
        ];
        
        //temporarily disable
        remove_filter('content_save_pre', 'wp_filter_post_kses');
        remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');

        if (wp_insert_post($post, false) != 0) {
            return true;
        } else {
            return false;
        }
        
        //bring it back once you're done posting
        add_filter('content_save_pre', 'wp_filter_post_kses');
        add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    }
}
