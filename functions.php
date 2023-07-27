<?php

/**
 * -----------------------------------------------------------------------------------------------------------
 */
define('BR_THEME_VERSION', '1.0.0');
define('BR_THEME_TEXT_DOMAIN', wp_get_theme()->get('TextDomain'));

function log_stuff($stuff_to_log)
{
    error_log(print_r($stuff_to_log, true));
}

/**
 * -------------------------------------------------------------------------------------------------------------------------------
 * Add signiture to admin footer
 */
add_filter('admin_footer_text', 'footer_admin');
function footer_admin()
{
    echo '<span id="footer-thankyou">' . __('Developed by ', BR_THEME_TEXT_DOMAIN) . '<a href="https://www.brandsite.lv" target="_blank" rel="nofollow">Brandsite.lv</a></span>';
}

/**
 * -------------------------------------------------------------------------------------------------------------------------------
 * Styles & scripts
 */

//fontend css
add_action('wp_enqueue_scripts', 'my_enqueue_styles');
function my_enqueue_styles()
{
    // enqueue parent styles
    wp_enqueue_style('parent-theme', get_template_directory_uri() . '/style.css');

    // enqueue child styles
    wp_enqueue_style('child-theme', get_stylesheet_directory_uri() . '/style.css', array(), BR_THEME_VERSION, "all");
}

//admin css
add_action('admin_enqueue_scripts', 'admin_style');
function admin_style()
{
    wp_enqueue_style('br-admin-style', get_stylesheet_directory_uri() . '/admin-style.css', [], BR_THEME_VERSION);
}


//child theme JS
// add_action('wp_enqueue_scripts', 'theme_js');
// function theme_js()
// {
//     wp_enqueue_script('theme_js', get_stylesheet_directory_uri() . '/JS/app.js', array(), BR_THEME_VERSION, "all");
// }


/**
 * -----------------------------------------------------------------------------------------------------------
 * Add SVG support
 */
function svg_mime_type($mimes = array())
{
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'svg_mime_type');


/**
 * -----------------------------------------------------------------------------------------------------------
 * Optimize speed and remove the unwanted stuff
 */
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
    'comment_support_post'  => false,
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


/**
 * -----------------------------------------------------------------------------------------------------------
 * Custom post types 
 *-----------------------
 * Example for creating post-type "Projekti" with custom taxonomies
 */
// require_once get_theme_file_path() . '/BrPostType.php';
// $portfolio = new BrPostType(
//     array(
//         'name'          => 'Projekti',
//         'singular_name' => 'Projekts',
//         'slug'          => 'projekts',
//         'menu_position' => 3,
//         'menu_icon'     => 'dashicons-portfolio',
//         'taxonomies'    => array(
//             array(
//                 'singular_name'       => 'Valsts',
//                 'plural_name'         => 'Valstis',
//                 'slug'                => 'valsts',
//                 'hierarchical'        => true,
//             ),
//             array(
//                 'singular_name'       => 'Kategorija',
//                 'plural_name'         => 'Kategorijas',
//                 'slug'                => 'kategorija',
//                 'hierarchical'        => true,
//             )
//         )
//     )
// );
// $portfolio->hooks();

/**
 * -----------------------------------------------------------------------------------------------------------
 */
// add_filter('register_post_type_args', 'remove_default_post_type', 0, 2);
// function remove_default_post_type($args, $postType)
// {
//     if ($postType === 'post') {
//         $args['public']                = false;
//         $args['show_ui']               = false;
//         $args['show_in_menu']          = false;
//         $args['show_in_admin_bar']     = false;
//         $args['show_in_nav_menus']     = false;
//         $args['can_export']            = false;
//         $args['has_archive']           = false;
//         $args['exclude_from_search']   = true;
//         $args['publicly_queryable']    = false;
//         $args['show_in_rest']          = false;
//     }

//     return $args;
// }
