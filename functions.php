<?php

define('BR_THEME_VERSION', '1.0.0');

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
    'emoji'                 => false,
    'oembed'                => false,
    'jQuery_migrate'        => false,
    'new_content_admin_bar' => false,
    'archive_admin_bar'     => false,
    'editor_page'           => false, //remove editor from pages
    'comments_admin_menu'   => false,
    'comments_admin_bar'    => false,
    'comment_support_page'  => false,
    'comment_support_post'  => false
));
$br_optimize->hooks();
