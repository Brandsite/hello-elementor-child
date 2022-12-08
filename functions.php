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