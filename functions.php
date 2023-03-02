<?php

define('BR_THEME_VERSION', '1.0.0');
function log_stuff($stuff_to_log)
{
    error_log(print_r($stuff_to_log, true));
}

function my_enqueue_styles()
{

    // enqueue parent styles
    wp_enqueue_style('parent-theme', get_template_directory_uri() . '/style.css');

    // enqueue child styles
    wp_enqueue_style('child-theme', get_stylesheet_directory_uri() . '/style.css', array(), BR_THEME_VERSION, "all");
}
add_action('wp_enqueue_scripts', 'my_enqueue_styles');


/*Child theme JS*/
// function theme_js()
// {
//     wp_enqueue_script('theme_js', get_stylesheet_directory_uri() . '/JS/app.js', array(), BR_THEME_VERSION, "all");
// }
// add_action('wp_enqueue_scripts', 'theme_js');


//add SVG support
function svg_mime_type($mimes = array())
{
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'svg_mime_type');

//Optimize speed and remove the unwanted stuff
require_once get_theme_file_path() . '/BrOptimize.php';

$br_optimize = new BrOptimize(array(
    'emoji'                 => true,
    'oembed'                => true,
    'jQuery_migrate'        => true,
    'new_content_admin_bar' => true,
    'archive_admin_bar'     => true,
    'editor_page'           => true, //remove editor from pages
    'comments_admin_menu'   => true,
    'comments_admin_bar'    => true,
    'comment_support_page'  => true,
    'comment_support_post'  => true,
    'defer_javascript'      => false,

    //exceptions based on .js file URL. add a file name, full URL or part of the URL
    'defer_exceptions_file' => array(
        'jquery.min.js',
        'cdn-cookieyes',
        'wp-includes/js',
        'woocommerce-ultimate-gift-card-product-single.js',
    ),

    //exceptions based on page. add page ID - e.g. 66 or page title - e.g. 'Contacts page'
    'defer_exceptions_page' => array(
//         'Blogs'
    )
));
$br_optimize->hooks();
