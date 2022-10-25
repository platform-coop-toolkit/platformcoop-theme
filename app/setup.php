<?php

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('platformcoop/main.css', asset_path('styles/main.css'), false, null);
    wp_enqueue_script('platformcoop/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    $cf7 = false;
    if (is_singular()) {
        $post = get_post();
        if (has_shortcode($post->post_content, 'contact-form-7')) {
            $cf7 = true;
        }
    }

    if ($cf7) {
        \wpcf7_enqueue_scripts();
    }
}, 100);

/**
 * Block styles
 */
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'platformcoop/block-styles.js',
        asset_path('scripts/block-styles.js'),
        ['wp-blocks', 'wp-dom'],
        null,
        true
    );
});

/**
 * Remove Emoji
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

/**
 * Output root colours
 */
add_action('wp_head', function () {
    echo get_color_vars();
});

/**
 * Output root colours
 */
add_action('admin_head', function () {
    echo get_color_vars();
});

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Add theme support for Wide Alignment
     * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#wide-alignment
     */
    add_theme_support('align-wide');

    /**
     * Add editor styles
     * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#editor-styles
     */
    add_theme_support('editor-styles');
    add_editor_style(asset_path('styles/editor.css'));

    /**
     * Enable responsive embeds
     * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable feed links
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#feed-links
     */
    add_theme_support('automatic-feed-links');

    /**
     * Dequeue Gutenberg CSS
     * @link https://wordpress.org/gutenberg/?s=dequeue (404)
     */
    add_action('wp_enqueue_scripts', function () {
        wp_dequeue_style('wp-block-library');
    }, 100);

    /**
     * Disable block editor color palettes
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-custom-colors-in-block-color-palettes
     */
    add_theme_support('disable-custom-colors');

    /**
     * Add color palette support
     */
    add_theme_support('editor-color-palette', (block_vars())->colors);

    /**
     * Disable custom block editor font sizes
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-custom-font-sizes
     */
    add_theme_support('disable-custom-font-sizes');

    /**
     * Set font sizes for the editor
     * @link https://github.com/WordPress/gutenberg/blob/master/docs/designers-developers/developers/themes/theme-support.md#block-font-sizes
     */
    add_theme_support('editor-font-sizes', []);

    /**
     * Enable responsive embeds
     * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'pcc')
    ]);
    register_nav_menus([
        'footer_navigation' => __('Footer Navigation', 'pcc')
    ]);

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Support excerpts for pages.
     */
    add_post_type_support('page', 'excerpt');

    /**
     * Image sizes.
     */
    add_image_size('social', 650, 400, ['center', 'center']);
    add_image_size('person-mobile', 260, 150, ['center', 'center']);
    add_image_size('person-mobile@2x', 520, 300, ['center', 'center']);
    add_image_size('person-desktop', 367, 250, ['center', 'center']);
    add_image_size('post-thumbnail', 367, 165, ['center', 'center']);
    add_image_size('person-desktop@2x', 734, 500, ['center', 'center']);
    add_image_size('person-profile', 675, 555, ['center', 'center']);
    add_image_size('person-profile@2x', 1350, 1110, ['center', 'center']);
    add_image_size('banner', 852, 568, ['center', 'center']);
    add_image_size('event-banner-mobile', 400, 225, ['center', 'center']);
    add_image_size('event-banner', 2720, 600, ['center', 'center']);

    /**
     * Add localization support
     * @see https://roots.io/sage/docs/theme-localization/
     */
    load_theme_textdomain('pcc', get_template_directory() . '/lang');

    /**
     * Add custom role for external users
     */
    add_role('external_user', __('External User', 'pcc'), []);

}, 20);

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});

add_action('init', function () {
    global $editor_styles;
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (Container $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }
        (new BladeProvider($app))->register();
        return new Blade($app['view']);
    });

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= " . __NAMESPACE__ . "\\asset_path({$asset}); ?>";
    });
});

add_action('init', function () {
    add_rewrite_rule(
        '^events/([^/]+)/participants/?$',
        'index.php?pcc-event=$matches[1]&participants=random',
        'top'
    );
    add_rewrite_rule(
        '^events/([^/]+)/participants/alphabetical/?$',
        'index.php?pcc-event=$matches[1]&participants=alphabetical',
        'top'
    );
    add_rewrite_rule(
        '^events/([^/]+)/participants/([^/]+)/?$',
        'index.php?pcc-person=$matches[2]&event=$matches[1]',
        'top'
    );
    add_rewrite_rule(
        '^events/([^/]+)/program/?$',
        'index.php?pcc-event=$matches[1]&program=yes',
        'top'
    );
});

add_action('admin_head', function () {
    echo template('partials.favicon');
});

add_action('init', function () {
    if (isset($_GET['post'])) {
        $post_id = $_GET['post'];
    } elseif (isset($_POST['post_ID'])) {
        $post_id = $_POST['post_ID'];
    } else {
        return;
    }
    $template_file = get_post_meta($post_id, '_wp_page_template', true);
    switch ($template_file) {
        case 'views/page-people.blade.php':
        case 'views/page-photo-credits.blade.php':
        case 'views/page-stories.blade.php':
            remove_post_type_support('page', 'editor');
            break;
        default:
            break;
    }
});

add_action('edit_form_after_title', function () {
    if (isset($_GET['post'])) {
        $post_id = $_GET['post'];
    } elseif (isset($_POST['post_ID'])) {
        $post_id = $_POST['post_ID'];
    } else {
        return;
    }
    $template_file = get_post_meta($post_id, '_wp_page_template', true);
    switch ($template_file) {
        case 'views/page-people.blade.php':
            echo sprintf(
                '<div class="notice notice-warning inline"><p>%s</p></div>',
                __('You are currently editing the page that displays People.', 'pcc')
            );
            break;
        case 'views/page-photo-credits.blade.php':
            echo sprintf(
                '<div class="notice notice-warning inline"><p>%s</p></div>',
                __('You are currently editing the page that displays photo credits.', 'pcc')
            );
            break;
        case 'views/page-stories.blade.php':
            echo sprintf(
                '<div class="notice notice-warning inline"><p>%s</p></div>',
                __('You are currently editing the page that displays Community Stories.', 'pcc')
            );
            break;
        default:
            break;
    }
});
