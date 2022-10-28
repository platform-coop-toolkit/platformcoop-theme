<?php

// Localize and register script with data for a JS variable
wp_enqueue_script('filter_events_script', get_template_directory_uri() . '/assets/js/filter_events.js', ['jquery'], '1.0', true);
wp_localize_script('filter_events_script', 'filter_events_script_ajax', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce'  => wp_create_nonce('ajax_nonce')]);

// Ajax function
function filter_events() {
    $category = $_POST['category'];
    $currentPage = $_POST['currentPage'];

    $meta_query = '';
    $offset = ($currentPage - 1) * 6;

    if ($category != 'all') {
        $meta_query = array(
            array(
                'key' => 'pcc_event_type',
                'value' => $category,
                'compare' => '='
            )
        );
    }

    global $paged;

    $events = new \WP_Query([
        'post_type' => 'pcc-event',
        'posts_per_page' => 6,
        'offset' => $offset,
        'post_status' => 'publish',
        'paged' => $paged,
        'meta_query' => $meta_query
    ]);

    foreach ($events->posts as $key => $event) {
        $events->posts[$key]->link = get_permalink($event->ID);
        $events->posts[$key]->img = get_the_post_thumbnail_url($event->ID) ?? null;
        $events->posts[$key]->start =  date('M j, Y', get_post_meta($event->ID, 'pcc_event_start')[0]) ?? null;
    }

    $args_paginate = [
        'base' => get_permalink() . '/pcc/events' . '%_%?cat=' . $category,
        'format' => '/page/%#%',
        'current' => $currentPage,
        'total' => $events->max_num_pages,
        'mid_size' => 1,
        'prev_text' => __('‹ '),
        'next_text' => __(' ›'),
    ];
    
    $paginate_links = paginate_links($args_paginate);
    $events->pagination = $paginate_links ?? null;

    return wp_send_json_success($events);
}

add_action('wp_ajax_nopriv_filter_events', 'filter_events');
add_action('wp_ajax_filter_events', 'filter_events');
