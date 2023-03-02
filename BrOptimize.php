<?php

/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BrOptimize')) {

    class BrOptimize
    {
        /**
         * Optimization settings
         * @var array
         */
        protected $settings;

        /**
         * Default settings
         * @var array
         */
        protected $defaults = array(
            'emoji'                 => false,
            'oembed'                => false,
            'jQuery_migrate'        => false,
            'new_content_admin_bar' => false,
            'archive_admin_bar'     => false,
            'editor_page'           => false,
            'comments_admin_menu'   => false,
            'comments_admin_bar'    => false,
            'comment_support_page'  => false,
            'defer_video'           => false,
            'defer_javascript'      => true,
            'defer_exceptions_file' => array(),
            'defer_exceptions_page' => array()
        );

        /**
         * -----------------------------------------------------------
         * Merge default settings with arguments
         */
        function __construct($args = array())
        {

            $this->settings = wp_parse_args($args, $this->defaults);
        }

        /**
         * -----------------------------------------------------------
         * Hooks
         */
        function hooks()
        {
            add_action('init', [$this, 'remove_oembed'], 9999);

            add_action('wp_default_scripts', [$this, 'dequeue_jquery_migrate']);

            add_action('admin_menu', [$this, 'remove_coments_from_admin_menu']);

            add_action('init', [$this, 'remove_comment_support']);

            add_action('wp_before_admin_bar_render', [$this, 'remove_stuff_from_admin_bar']);

            add_action('init', [$this, 'remove_editor_from_page']);

            add_action('wp_enqueue_scripts', [$this, 'remove_gutenberg_css']);

            add_filter('wp_headers', [$this, 'disable_x_pingback']);

            add_filter('clean_url', [$this, 'defer_javascript'], 11, 1);

            $this->remove_wp_emoji();

            $this->remove_gutenberg();

            $this->remove_unwanted_stuff();
        }


        /**
         * -----------------------------------------------------------
         * Remove WordPress emoji
         */
        function remove_wp_emoji()
        {
            if (!$this->settings['emoji']) {
                remove_action('wp_head', 'print_emoji_detection_script', 7);
                remove_action('wp_print_styles', 'print_emoji_styles');

                remove_action('admin_print_scripts', 'print_emoji_detection_script');
                remove_action('admin_print_styles', 'print_emoji_styles');
            }
        }

        /**
         * -----------------------------------------------------------
         */
        private function remove_unwanted_stuff()
        {
            add_filter('xmlrpc_enabled', '__return_false');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'rsd_link');
        }

        /**
         * -----------------------------------------------------------
         */
        function disable_x_pingback($headers)
        {
            unset($headers['X-Pingback']);

            return $headers;
        }

        /**
         * -----------------------------------------------------------
         */
        function remove_oembed()
        {
            if (!$this->settings['oembed']) {

                // Remove the REST API endpoint.
                remove_action('rest_api_init', 'wp_oembed_register_route');

                // Turn off oEmbed auto discovery.
                add_filter('embed_oembed_discover', '__return_false');

                // Don't filter oEmbed results.
                remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

                // Remove oEmbed discovery links.
                remove_action('wp_head', 'wp_oembed_add_discovery_links');

                // Remove oEmbed-specific JavaScript from the front-end and back-end.
                remove_action('wp_head', 'wp_oembed_add_host_js');
                add_filter('tiny_mce_plugins', [$this, 'disable_embeds_tiny_mce_plugin']);

                // Remove all embeds rewrite rules.
                add_filter('rewrite_rules_array', [$this, 'disable_embeds_rewrites']);

                // Remove filter of the oEmbed result before any HTTP requests are made.
                remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);

                add_filter('clean_url', [$this, 'defer_javascript'], 11, 1);
            }
        }

        /**
         * -----------------------------------------------------------
         */
        function disable_embeds_tiny_mce_plugin($plugins)
        {
            return array_diff($plugins, array('wpembed'));
        }

        /**
         * -----------------------------------------------------------
         */
        function disable_embeds_rewrites($rules)
        {
            foreach ($rules as $rule => $rewrite) {
                if (false !== strpos($rewrite, 'embed=true')) {
                    unset($rules[$rule]);
                }
            }
            return $rules;
        }

        /**
         * -----------------------------------------------------------
         * Remove jQuery-migrate from frontend
         */
        function dequeue_jquery_migrate($scripts)
        {
            if (!$this->settings['jQuery_migrate']) {

                if (!is_admin() && !empty($scripts->registered['jquery'])) {
                    $scripts->registered['jquery']->deps = array_diff(
                        $scripts->registered['jquery']->deps,
                        ['jquery-migrate']
                    );
                }
            }
        }

        /**
         * -----------------------------------------------------------
         * Remove comments from admin menu
         */
        function remove_coments_from_admin_menu()
        {
            if (!$this->settings['comments_admin_menu']) {

                remove_menu_page('edit-comments.php');
            }
        }

        /**
         * -----------------------------------------------------------
         * Remove comments from post and pages 
         */
        function remove_comment_support()
        {
            if (!$this->settings['comment_support_post']) {
                remove_post_type_support('post', 'comments');
            }

            if (!$this->settings['comment_support_page']) {
                remove_post_type_support('page', 'comments');
            }
        }

        /**
         * -----------------------------------------------------------
         * Remove stuff from admin bar
         */
        function remove_stuff_from_admin_bar()
        {
            global $wp_admin_bar;
            if (!$this->settings['comments_admin_bar']) {
                $wp_admin_bar->remove_menu('comments');
            }

            if (!$this->settings['new_content_admin_bar']) {
                $wp_admin_bar->remove_menu('new-content');
            }

            if (!$this->settings['archive_admin_bar']) {
                $wp_admin_bar->remove_menu('archive');
            }
        }

        /**
         * -----------------------------------------------------------
         * Remove editor from pages
         * */
        function remove_editor_from_page()
        {
            if (!$this->settings['editor_page']) {
                remove_post_type_support('page', 'editor');
            }
        }

        /**
         * -----------------------------------------------------------
         */
        private function remove_gutenberg()
        {
            add_filter('use_block_editor_for_post', '__return_false', 10);
        }

        /**
         * -----------------------------------------------------------
         */
        function remove_gutenberg_css()
        {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
            // wp_dequeue_style('wc-block-style'); // REMOVE WOOCOMMERCE BLOCK CSS
            wp_dequeue_style('global-styles'); // REMOVE THEME.JSON
            wp_dequeue_style('classic-theme-styles');
        }

        /**
         * ------------------------------------------------------------
         * Defer JS
         */
        function defer_javascript($url)
        {
            if ($this->settings['defer_javascript']) {
                if (is_admin()) return $url;

                if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    if (is_checkout() || is_cart() || is_account_page()) return $url;
                }

                if (FALSE === strpos($url, '.js')) return $url;

                foreach ($this->settings['defer_exceptions_file'] as $part_of_url) {
                    if (strpos($url, $part_of_url)) return $url;
                }

                foreach ($this->settings['defer_exceptions_page'] as $page) {
                    global $post;
                    if ($post->ID == $page || $post->post_title == $page) return $url;
                }

                return "$url' defer ";
            } else {
                return $url;
            }
        } //Optimize
    }
}
