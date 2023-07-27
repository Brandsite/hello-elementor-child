<?php

/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

class BrPostType
{
    /**
     * Default settings
     * @var array
     */
    protected $defaults = array(
        'name'                => 'Post types',
        'singular_name'       => 'Post type',
        'description'         => '',
        'public'              => true,
        'hierarchical'        => false,
        'exclude_from_search' => false,
        'show_in_menu'        => true,
        'has_archive'         => true,
        'slug'                => 'posttype',
        'show_in_rest'        => true,
        'menu_position'       => 2,
        'menu_icon'           => 'dashicons-admin-multisite',
        'capability_type'     => 'post',
        'editor'              => false,
        'taxonomies'          => array(
            array(
                'singular_name'       => 'Cat label',
                'plural_name'         => 'Cat labels',
                'slug'                => 'cat-label',
                'hierarchical'        => true,
            )
        )
    );


    /**
     * Custom post type settings
     * @var array
     */
    protected $settings = array();

    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     * Constructor
     */
    function __construct($args = array())
    {

        if (empty($args)) return;
        if (!isset($args['name']) || empty($args['name'])) return;
        if (!isset($args['slug']) || $args['slug'] == '') return;

        /**
         * Merge default settings with arguments
         */
        $this->settings = wp_parse_args($args, $this->defaults);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     * Hooks
     */
    function hooks()
    {
        add_action('init', [$this, 'create_posttypes'], 10);

        add_action('init', [$this, 'remove_editor']);

        add_action('init', [$this, 'create_taxonomies']);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     */
    function create_taxonomies()
    {
        foreach ($this->settings['taxonomies'] as $taxonomy) {
            register_taxonomy(
                $taxonomy['slug'],
                $this->settings['slug'],
                array(
                    'labels'  => [
                        'name'              => _x($taxonomy['plural_name'], 'taxonomy general name', BR_THEME_TEXT_DOMAIN),
                        'singular_name'     => _x($taxonomy['singular_name'], 'taxonomy singular name', BR_THEME_TEXT_DOMAIN),
                        'search_items'      => __('Search ' . $taxonomy['plural_name'], BR_THEME_TEXT_DOMAIN),
                        'all_items'         => __('All ' . $taxonomy['plural_name'], BR_THEME_TEXT_DOMAIN),
                        'view_item'         => __('View ' . $taxonomy['singular_name'], BR_THEME_TEXT_DOMAIN),
                        'parent_item'       => __('Parent ' . $taxonomy['singular_name'], BR_THEME_TEXT_DOMAIN),
                        'parent_item_colon' => __('Parent ' . $taxonomy['singular_name'] . ':', BR_THEME_TEXT_DOMAIN),
                        'edit_item'         => __('Edit ' . $taxonomy['singular_name'], BR_THEME_TEXT_DOMAIN),
                        'update_item'       => __('Update ' . $taxonomy['singular_name'], BR_THEME_TEXT_DOMAIN),
                        'add_new_item'      => __('Add New ' . $taxonomy['singular_name'], BR_THEME_TEXT_DOMAIN),
                        'new_item_name'     => __('New ' . $taxonomy['singular_name'] . ' Name', BR_THEME_TEXT_DOMAIN),
                        'not_found'         => __('No ' . $taxonomy['plural_name'] . ' Found', BR_THEME_TEXT_DOMAIN),
                        'back_to_items'     => __('Back to ' . $taxonomy['plural_name'], BR_THEME_TEXT_DOMAIN),
                        'menu_name'         => __($taxonomy['singular_name'], BR_THEME_TEXT_DOMAIN),
                    ],
                    'rewrite'             => array('slug' => $taxonomy['slug']),
                    'hierarchical'        => $taxonomy['hierarchical'],
                ),
            );
        }
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     */
    function create_posttypes()
    {
        register_post_type(
            $this->settings['slug'],
            array(
                'labels' => array(
                    'name'                  => _x($this->settings['name'], 'Post type general name', BR_THEME_TEXT_DOMAIN),
                    'singular_name'         => _x($this->settings['singular_name'], 'Post type singular name', BR_THEME_TEXT_DOMAIN),
                    'menu_name'             => _x($this->settings['name'], 'Admin Menu text', BR_THEME_TEXT_DOMAIN),
                    'name_admin_bar'        => _x($this->settings['singular_name'], 'Add New on Toolbar', BR_THEME_TEXT_DOMAIN),
                    'add_new'               => __('Add New ' . strtolower($this->settings['singular_name'])),
                    'add_new_item'          => __('Add New ' . strtolower($this->settings['singular_name']), BR_THEME_TEXT_DOMAIN),
                    'new_item'              => __('New ' . strtolower($this->settings['singular_name']), BR_THEME_TEXT_DOMAIN),
                    'edit_item'             => __('Edit ' . strtolower($this->settings['singular_name']), BR_THEME_TEXT_DOMAIN),
                    'view_item'             => __('View ' . strtolower($this->settings['singular_name']), BR_THEME_TEXT_DOMAIN),
                    'all_items'             => __('All ' . $this->settings['name'], BR_THEME_TEXT_DOMAIN),
                    'search_items'          => __('Search ' . $this->settings['name'], BR_THEME_TEXT_DOMAIN),
                    'parent_item_colon'     => __('Parent ' . $this->settings['name'] . ':', BR_THEME_TEXT_DOMAIN),
                    'not_found'             => __('No ' . $this->settings['name'] . ' found.', BR_THEME_TEXT_DOMAIN),
                    'not_found_in_trash'    => __('No ' . $this->settings['name'] . ' found in Trash.', BR_THEME_TEXT_DOMAIN),
                    'featured_image'        => _x($this->settings['singular_name'] . ' Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', BR_THEME_TEXT_DOMAIN),
                    'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', BR_THEME_TEXT_DOMAIN),
                    'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', BR_THEME_TEXT_DOMAIN),
                    'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', BR_THEME_TEXT_DOMAIN),
                    'archives'              => _x($this->settings['singular_name'] . ' archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', BR_THEME_TEXT_DOMAIN),
                    'insert_into_item'      => _x('Insert into ' . strtolower($this->settings['singular_name']), 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', BR_THEME_TEXT_DOMAIN),
                    'uploaded_to_this_item' => _x('Uploaded to this ' . strtolower($this->settings['singular_name']), 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', BR_THEME_TEXT_DOMAIN),
                    'filter_items_list'     => _x('Filter ' . $this->settings['name'] . ' list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', BR_THEME_TEXT_DOMAIN),
                    'items_list_navigation' => _x($this->settings['name'] . ' list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', BR_THEME_TEXT_DOMAIN),
                    'items_list'            => _x($this->settings['name'] . ' list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', BR_THEME_TEXT_DOMAIN),
                ),
                'public'        => $this->settings['public'],
                'has_archive'   => $this->settings['has_archive'],
                'rewrite'       => array('slug' => $this->settings['slug'],),
                'show_in_rest'  => $this->settings['show_in_rest'],
                'menu_icon'     => $this->settings['menu_icon'],
                'menu_position' => $this->settings['menu_position'],
            )
        );
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     * Remove the text editor from backend
     */
    function remove_editor()
    {
        if ($this->settings['editor'] === false) {
            remove_post_type_support($this->settings['slug'], 'editor');
        }
    }
} //PostType
