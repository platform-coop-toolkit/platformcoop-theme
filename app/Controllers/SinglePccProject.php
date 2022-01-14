<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class SinglePccProject extends Controller
{
    public static function projectTitle()
    {
        global $id, $post;
        return SinglePccProject::rootPage($id, $post)->post_title;
    }

    public static function rootPage($id, $post)
    {
        $root_id = "";

        if ($post->post_parent) {
            $ancestors = get_post_ancestors($id);
            $root_id = (!empty($ancestors) ? array_pop($ancestors): $post_id);
        } else {
            $root_id = $id;
        }
        return get_post($root_id);
    }

    public static function ancestors()
    {
        global $id, $post;
        $ancestors = array_reverse(get_post_ancestors($id));
        $output = [];

        foreach ($ancestors as $a_id) {
            $name = get_the_title($a_id);
            $output[$name] = [
            'name' => $name,
            'url' => get_permalink($a_id)
            ];
        }
        return $output;
    }

    public static function banner()
    {
        global $id, $post;
        $banner = "";

        if ($post->post_parent) {
            $ancestors = get_post_ancestors($id);
            $root_id = (!empty($ancestors) ? array_pop($ancestors): $post_id);
            $banner = get_post($root_id)->post_title;
        } else {
            $title = $post->post_title;
        }

        return $title;
    }

    public static function menuName()
    {
        global $id, $post;
        $output = [];

        $root_page = SinglePccProject::rootPage($id, $post);

        $menu_name = get_post_meta($root_page->ID, 'project_menu_name', true);

        return $menu_name;
    }

    public static function researchers()
    {
        global $id, $wp;
        $output = [];

        $project_id = get_post_meta($id, 'pcc_project_id', true);

        $researchers = get_post_meta($project_id, 'pcc_project_researchers', true);

        if ($researchers) {
            foreach ($researchers as $researcher_id) {
                $researcher = get_post($researcher_id);
                $output[ $researcher->post_title ] = [
                    'name' => $researcher->post_title,
                    'short_title' => get_post_meta($researcher_id, 'pcc_person_short_title', true),
                    'slug' => $researcher->post_name,
                    'page_link' => (get_page_by_path('teste-de-pessoa')) ? get_page_link($researcher_id) : '#',
                ];
            }
        }

        return $output;
    }
}
