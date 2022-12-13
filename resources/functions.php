<?php

/**
 * Do not edit anything in this file unless you know what you're doing
 */

use Roots\Sage\Config;
use Roots\Sage\Container;
use app\Controllers\Events;

/**
 * Helper function for prettying up errors
 * @param string $message
 * @param string $subtitle
 * @param string $title
 */
$sage_error = function ($message, $subtitle = '', $title = '') {
    $title = $title ?: __('Theme &rsaquo; Error', 'pcc');
    $footer = '<a href="https://roots.io/sage/docs/">roots.io/sage/docs/</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    wp_die($message, $title);
};

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('7.1', phpversion(), '>=')) {
    $sage_error(__('You must be using PHP 7.1 or greater.', 'pcc'), __('Invalid PHP version', 'pcc'));
}

/**
 * Ensure compatible version of WordPress is used
 */
if (version_compare('4.7.0', get_bloginfo('version'), '>=')) {
    $sage_error(
        __('You must be using WordPress 4.7.0 or greater.', 'pcc'),
        __('Invalid WordPress version', 'pcc')
    );
}

/**
 * Ensure dependencies are loaded
 */
if (!class_exists('Roots\\Sage\\Container')) {
    if (!file_exists($composer = __DIR__ . '/../vendor/autoload.php')) {
        $sage_error(
            __('You must run <code>composer install</code> from the theme directory.', 'pcc'),
            __('Autoloader not found.', 'pcc')
        );
    }
    require_once $composer;
}

/**
 * Sage required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
array_map(function ($file) use ($sage_error) {
    $file = "../app/{$file}.php";
    if (!locate_template($file, true, true)) {
        $sage_error(sprintf(__('Error locating <code>%s</code> for inclusion.', 'pcc'), $file), 'File not found');
    }
}, ['helpers', 'setup', 'filters', 'admin']);

/**
 * Here's what's happening with these hooks:
 * 1. WordPress initially detects theme in themes/sage/resources
 * 2. Upon activation, we tell WordPress that the theme is actually in themes/sage/resources/views
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/sage/resources
 *
 * We do this so that the Template Hierarchy will look in themes/sage/resources/views for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/sage/resources
 *
 * This is not compatible with the WordPress Customizer theme preview prior to theme activation
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/sage/resources
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/sage/resources
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/sage/resources/views
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/sage/resources
 */
array_map(
    'add_filter',
    ['theme_file_path', 'theme_file_uri', 'parent_theme_file_path', 'parent_theme_file_uri'],
    array_fill(0, 4, 'dirname')
);
Container::getInstance()
    ->bindIf('config', function () {
        return new Config([
            'assets' => require dirname(__DIR__) . '/config/assets.php',
            'theme' => require dirname(__DIR__) . '/config/theme.php',
            'view' => require dirname(__DIR__) . '/config/view.php',
            'blocks' => require dirname(__DIR__) . '/config/blocks.php',
        ]);
    }, true);

/**
 * Start the Carbon Fields
 */
require get_template_directory() . '/../custom-fields/init.php';


/**
 * Checks if the user has a certain role
 *
 * @param int $user_id
 * @param string $role_name
 * @return bool
 */
function user_has_role($role_name)
{
    $user_id = get_current_user_id();

    if (is_user_logged_in()) {
        $user_meta = get_userdata($user_id);
        $user_roles = $user_meta->roles;
        return in_array($role_name, $user_roles);
    }
    return false;
}


/**
 * Log-in via AJAX
 *
 * @return void
 */
function login_menu()
{
    $user_by_email = get_user_by('email', $_POST['log']);
    $user_name = !empty($user_by_email->user_login) ? $user_by_email->user_login : $_POST['log']; 
    $password = $_POST['pwd'];
    $rememberme = $_POST['rememberme'];

    $user = wp_signon([
        'user_login' => $user_name,
        'user_password' => $password,
        'remember' => $rememberme,
    ]);

    if (is_wp_error($user)) {
        wp_send_json_error([
            'message' => $user->get_error_message()
        ]);
    }
    wp_send_json_success(true);
}
add_action('wp_ajax_nopriv_login_menu', 'login_menu', 0);
add_action('wp_ajax_login_menu', 'login_menu', 0);


/**
 * Log-out via AJAX
 *
 * @return void
 */
function logout_menu()
{
    wp_logout();
    wp_send_json(true);
}
add_action('wp_ajax_nopriv_logout_menu', 'logout_menu', 0);
add_action('wp_ajax_logout_menu', 'logout_menu', 0);


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('login', get_template_directory_uri() . '/assets/scripts/login.js', array('jquery'), false, true);
    wp_localize_script('login', 'wpObj', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ));
});




