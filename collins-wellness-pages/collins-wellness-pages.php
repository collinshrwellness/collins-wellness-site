<?php
/**
 * Plugin Name: Collins Wellness Pages
 * Description: Custom page templates for Collins Wellness — managed via GitHub.
 * Version: 1.0.0
 * Author: Collins Wellness
 */

if (!defined('ABSPATH')) exit;

class CollinsWellnessPages {

    public function __construct() {
        add_action('init', [$this, 'create_pages']);
        add_filter('the_content', [$this, 'maybe_inject_page']);
        add_action('wp_head', [$this, 'inject_styles']);
        add_action('wp_footer', [$this, 'inject_scripts']);
    }

    /**
     * Page definitions — add new pages here
     */
    private function pages() {
        return [
            'coherence-protocol' => [
                'title'  => 'The Coherence Protocol',
                'file'   => plugin_dir_path(__FILE__) . 'pages/coherence-protocol-body.html',
                'styles' => plugin_dir_path(__FILE__) . 'pages/coherence-protocol-styles.html',
            ],
        ];
    }

    /**
     * Auto-create pages in WordPress if they don't exist
     */
    public function create_pages() {
        foreach ($this->pages() as $slug => $page) {
            if (!get_page_by_path($slug)) {
                wp_insert_post([
                    'post_title'   => $page['title'],
                    'post_name'    => $slug,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_content' => '<!-- collins-wellness-page:' . $slug . ' -->',
                ]);
            }
        }
    }

    /**
     * Inject the HTML body content into the page
     */
    public function maybe_inject_page($content) {
        global $post;
        if (!$post) return $content;

        foreach ($this->pages() as $slug => $page) {
            if ($post->post_name === $slug && file_exists($page['file'])) {
                return file_get_contents($page['file']);
            }
        }
        return $content;
    }

    /**
     * Inject page-specific styles into <head>
     */
    public function inject_styles() {
        global $post;
        if (!$post) return;

        foreach ($this->pages() as $slug => $page) {
            if ($post->post_name === $slug && file_exists($page['styles'])) {
                echo file_get_contents($page['styles']);
            }
        }
    }

    /**
     * Inject Google Fonts link (needed for all pages)
     */
    public function inject_scripts() {
        global $post;
        if (!$post) return;

        foreach ($this->pages() as $slug => $page) {
            if ($post->post_name === $slug) {
                // Fonts already loaded via @import in styles, nothing extra needed
            }
        }
    }
}

new CollinsWellnessPages();
