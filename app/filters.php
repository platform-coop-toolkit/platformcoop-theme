<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add person class if needed */
    $benefits = get_page_by_path('about/benefits');
    if (function_exists('pll_get_post')) {
        if (is_page() && pll_get_post(get_post()->post_parent, 'en') === $benefits->ID) {
            $classes[] = 'page-persona';
        }
    } else {
        if (is_page() && get_post()->post_parent === $benefits->ID) {
            $classes[] = 'page-persona';
        }
    }

    /** Add child class to sessions */
    if (is_singular('pcc-event') && get_post()->post_parent) {
        $classes[] = 'pcc-event-session';
    }
    /** Add project class to project pages */
    $template = [];
    preg_match('/([a-z]*-[a-z]*-[a-z]*)/', get_page_template_slug(), $template);
    if (isset($template[0]) &&  ($template[0] == "page-project-plain" ||
      $template[0] == "single-pcc-project")) {
        $classes[] = 'project';
      // To enable javascript for responsive menu
        $classes[] = 'single-pcc-event';
    }

    /** Add stories class if showing a list of stories **/
    $stories = get_page_by_path('/voices/stories/');
    if (function_exists('pll_get_post')) {
        if ((is_page() && pll_get_post(get_post()->ID, 'en') === $stories->ID) ||
            (is_tax('pcc-sector') ||
            is_tax('pcc-region'))) {
                $classes[] = 'stories';
        }
    } else {
        if ((is_page() && get_post()->ID === $stories->ID) || (is_tax('pcc-sector') || is_tax('pcc-region'))) {
            $classes[] = 'stories';
        }
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "content" to post classes
 */
add_filter('post_class', function ($classes) {
    global $post;
    $classes[] = 'content';
    if ($post->post_parent) {
        $classes[] = 'child';
    }
    return $classes;
});

/**
 * Ensure Media & Text block has align attribute.
 */
add_filter('render_block_data', function ($block, $source_block) {
    if ($block['blockName'] === 'core/media-text') {
        if (!isset($source_block['attrs']['align'])) {
            $block['attrs']['align'] = 'wide';
        }
    }
    return $block;
}, 10, 2);

/**
 * Wrap alignwide & alignfull blocks with ".wp-block-wrap".
 */
add_filter('render_block', function ($block_content, $block) {
    // Only on the frontend and if alignment attribute is set.
    if (is_admin()) {
        return $block_content;
    }

    if ($block['blockName'] === 'core/columns') {
        $column_count = count($block['innerBlocks']);
        return str_replace(
            'class="wp-block-columns',
            sprintf('class="wp-block-columns has-%d-columns', $column_count),
            $block_content
        );
    }

    if (isset($block['attrs']['align']) && $block['attrs']['align'] == 'wide') {
        return $block_content = '<div class="wp-block-wrap wp-block-wide-wrap">'. $block_content .'</div>';
    } elseif (isset($block['attrs']['align']) && $block['attrs']['align'] == 'full') {
        return $block_content = '<div class="wp-block-wrap wp-block-full-wrap">'. $block_content .'</div>';
    }

    return $block_content;
}, 10, 2);

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'pcc') . '</a>';
});


/**
 * Minimize inserter interactions.
 * @link https://developer.wordpress.org/block-editor/developers/filters/block-filters/#managing-block-categories
 */
add_filter('block_categories_all', function ($categories, $post) {
    return [[
        'slug'  => 'blocks',
        'title' => __('Blocks', 'pcc'),
        'icon'  => '',
    ]];
}, 10, 2);

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__.'\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    collect(['get_header', 'wp_head'])->each(function ($tag) {
        ob_start();
        do_action($tag);
        $output = ob_get_clean();
        remove_all_actions($tag);
        add_action($tag, function () use ($output) {
            echo $output;
        });
    });
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory().'/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $data = collect(get_body_class())->reduce(function ($data, $class) use ($comments_template) {
        return apply_filters("sage/template/{$class}/data", $data, $comments_template);
    }, []);

    $theme_template = locate_template(["views/{$comments_template}", $comments_template]);

    if ($theme_template) {
        echo template($theme_template, $data);
        return get_stylesheet_directory().'/index.php';
    }

    return $comments_template;
}, 100);

/**
 * Remove image width/height attributes.
 */
add_filter('post_thumbnail_html', function ($html) {
    return preg_replace('/(width|height)="\d+"\s/', "", $html);
});

/**
 * Hide Gutenberg menu
 * @see https://github.com/WordPress/gutenberg/blob/9a682eddb9eeb814cba4c3da007194c95567ddaa/gutenberg.php#L69
 */
if (function_exists('gutenberg_menu')) {
    remove_action('admin_menu', 'gutenberg_menu');
}

/**
 * Unload Contact Form 7 assets
 */
add_filter('wpcf7_load_js', '__return_false');
add_filter('wpcf7_load_css', '__return_false');

add_filter('bladesvg', function () {
    return [
        'svg_path' => 'resources/svg',
        'spritesheet_path' => 'resources/svg/spritesheet.svg',
        'spritesheet_url' => '',
        'sprite_prefix' => '',
        'inline' => true,
        'class' => ''
    ];
});

add_filter('query_vars', function ($vars) {
    return ['participants', 'instructors', 'coaches','program', 'event', 'org', 'clear'] + $vars;
});

// TODO: Add rel="canonical" for participants pointing back to people page.

add_filter('pre_get_posts', function ($query) {
    if (!is_admin() && is_post_type_archive('pcc-person') && !empty($query->query['post_type']  == 'pcc-person')) {
        $query->set('meta_key', 'pcc_person_show_on_people');
        $query->set('meta_value', 'on');
    }

    if (!is_admin() && is_tax() && !empty($query->query['org']) && empty($query->query['clear'])) {
        $query->set('post_type', 'pcc-story');
        $query->set('meta_key', 'pcc_story_organization');
        $query->set('meta_value', $query->query['org']);
    }

    if (!is_admin() && is_post_type_archive(['pcc-event', 'pcc-person', 'pcc-story'])) {
        $query->set('lang', '');
    }
});

add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    if (is_array($size)) {
        $attr['sizes'] = $size[0] . 'px';
    }
    return $attr;
}, 25, 3);

add_filter('wp_nav_menu_items', function ($items, $args) {
    if ($args->theme_location === 'primary_navigation') {
        $items .= template('partials.language-switcher');
    }
    return $items;
}, 10, 2);

/**
 * Remove admin bar from logged-in external users
 */
add_filter('show_admin_bar', function () {
    $excluded_roles = [ 'external_user' ];
    return is_user_logged_in() && ! array_intersect( wp_get_current_user()->roles, $excluded_roles);
}, 26);
