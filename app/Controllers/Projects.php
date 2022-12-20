<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class Projects extends Controller
{

    public static function projects()
    {
        $output = [];

        /* must only be published! */
        $projects = get_posts([
            'post_type' => 'pcc-project',
            'order' => 'asc',
            'status' => 'published'
        ]);

        if ($projects) {
            foreach ($projects as $p) {
                $page_id = get_post_meta($p->ID, 'pcc_project_page_id', true);

                $output[$p->ID] = [
                  'title' => $p->post_title,
                  'image' => get_post_thumbnail_id($p->ID),
                  'slug' => get_post_field('post_name', $p->ID),
                  'page_link_id' => (!empty(get_page_by_path($p->post_name))) ? get_page_link($p->ID) : '#',
                ];
                $post_content = preg_replace("~<!--(.*?)-->~s", "", $p->post_content);
                $output[$p->ID]['content'] = (strlen($post_content) > 200) ?
                    substr($post_content, 0, 200).'...' :
                    $post_content;
                    
                $page_id = null;
            }
        }
        return $output;
    }
}
