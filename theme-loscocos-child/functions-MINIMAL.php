<?php
/**
 * Los Cocos Child Theme functions and definitions
 * Minimal version for debugging
 */

if (!defined('ABSPATH')) {
    exit;
}

function loscocos_child_enqueue_styles()
{
    wp_enqueue_style('loscocos-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('loscocos-child-style', get_stylesheet_directory_uri() . '/style.css', array('loscocos-style'));
}
add_action('wp_enqueue_scripts', 'loscocos_child_enqueue_styles');
