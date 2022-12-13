<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

function pages() {
    $uncat_categories = get_terms([
        'taxonomy' => 'category',
        'name' => 'Uncategorized',
        'hide_empty' => true,
    ]);
    $uncat_categories = array_column($uncat_categories, 'term_id');

    $args = [
        'hide_empty' => 1,
        'exclude' => $uncat_categories,
    ];
    $categories = array_column(get_categories($args), 'name', 'term_id');

    Container::make('post_meta', __('Custom fields'))
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'views/page-custom-blog.blade.php')
        ->add_fields([
            Field::make('select', 'category', __('Select a category'))
                ->set_options($categories)
        ]);
}

function events() {
    $events_types = [
        'all' => __('All', 'pcc-framework'),
        'community' => __('Community Event', 'pcc-framework'),
        'conference' => __('PCC Conference', 'pcc-framework'),
        'pcc' => __('PCC Event', 'pcc-framework'),
        'icde' => __('ICDE Event', 'pcc-framework'),
        'course' => __('Course', 'pcc-framework'),
        'past' => __('Past Course', 'pcc-framework'),
    ];
 
    Container::make('post_meta', __('PCC Events'))
        ->where('post_type', '=', 'page')
        ->where('post_template', '=', 'views/events.blade.php')
        ->add_fields([
            Field::make('select', 'crb_event_type', __('Select a event type'))
                ->set_options($events_types)
        ]);
}