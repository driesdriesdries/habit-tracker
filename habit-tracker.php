<?php
/**
 * Plugin Name: Habit Tracker
 * Plugin URI: http://yourwebsite.com/habit-tracker
 * Description: A plugin to track and manage habits.
 * Version: 1.0
 * Author: Andries Bester
 * Author URI: https://andriesbester.com/
 */

 function ht_register_habit_cpt() {

    $labels = array(
        'name'                  => _x('Habits', 'Post Type General Name', 'habit-tracker'),
        'singular_name'         => _x('Habit', 'Post Type Singular Name', 'habit-tracker'),
        'menu_name'             => __('Habits', 'habit-tracker'),
        'name_admin_bar'        => __('Habit', 'habit-tracker'),
        'archives'              => __('Habit Archives', 'habit-tracker'),
        'attributes'            => __('Habit Attributes', 'habit-tracker'),
        'parent_item_colon'     => __('Parent Habit:', 'habit-tracker'),
        'all_items'             => __('All Habits', 'habit-tracker'),
        'add_new_item'          => __('Add New Habit', 'habit-tracker'),
        'add_new'               => __('Add New', 'habit-tracker'),
        'new_item'              => __('New Habit', 'habit-tracker'),
        'edit_item'             => __('Edit Habit', 'habit-tracker'),
        'update_item'           => __('Update Habit', 'habit-tracker'),
        'view_item'             => __('View Habit', 'habit-tracker'),
        'view_items'            => __('View Habits', 'habit-tracker'),
        'search_items'          => __('Search Habit', 'habit-tracker'),
        'not_found'             => __('Not found', 'habit-tracker'),
        'not_found_in_trash'    => __('Not found in Trash', 'habit-tracker'),
        'featured_image'        => __('Featured Image', 'habit-tracker'),
        'set_featured_image'    => __('Set featured image', 'habit-tracker'),
        'remove_featured_image' => __('Remove featured image', 'habit-tracker'),
        'use_featured_image'    => __('Use as featured image', 'habit-tracker'),
        'insert_into_item'      => __('Insert into habit', 'habit-tracker'),
        'uploaded_to_this_item' => __('Uploaded to this habit', 'habit-tracker'),
        'items_list'            => __('Habits list', 'habit-tracker'),
        'items_list_navigation' => __('Habits list navigation', 'habit-tracker'),
        'filter_items_list'     => __('Filter habits list', 'habit-tracker'),
    );
    $args = array(
        'label'                 => __('Habit', 'habit-tracker'),
        'description'           => __('Custom Post Type for tracking habits', 'habit-tracker'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-chart-line',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );

    register_post_type('habit', $args);
}
add_action('init', 'ht_register_habit_cpt', 0);

function ht_register_daily_log_cpt() {

    $labels = array(
        'name'                  => _x('Daily Logs', 'Post Type General Name', 'habit-tracker'),
        'singular_name'         => _x('Daily Log', 'Post Type Singular Name', 'habit-tracker'),
        'menu_name'             => __('Daily Logs', 'habit-tracker'),
        'name_admin_bar'        => __('Daily Log', 'habit-tracker'),
        'archives'              => __('Item Archives', 'habit-tracker'),
        'attributes'            => __('Item Attributes', 'habit-tracker'),
        'parent_item_colon'     => __('Parent Item:', 'habit-tracker'),
        'all_items'             => __('All Daily Logs', 'habit-tracker'),
        'add_new_item'          => __('Add New Daily Log', 'habit-tracker'),
        'add_new'               => __('Add New', 'habit-tracker'),
        'new_item'              => __('New Daily Log', 'habit-tracker'),
        'edit_item'             => __('Edit Daily Log', 'habit-tracker'),
        'update_item'           => __('Update Daily Log', 'habit-tracker'),
        'view_item'             => __('View Daily Log', 'habit-tracker'),
        'view_items'            => __('View Daily Logs', 'habit-tracker'),
        'search_items'          => __('Search Daily Log', 'habit-tracker'),
        'not_found'             => __('Not found', 'habit-tracker'),
        'not_found_in_trash'    => __('Not found in Trash', 'habit-tracker'),
        'featured_image'        => __('Featured Image', 'habit-tracker'),
        'set_featured_image'    => __('Set featured image', 'habit-tracker'),
        'remove_featured_image' => __('Remove featured image', 'habit-tracker'),
        'use_featured_image'    => __('Use as featured image', 'habit-tracker'),
        'insert_into_item'      => __('Insert into item', 'habit-tracker'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'habit-tracker'),
        'items_list'            => __('Daily Logs list', 'habit-tracker'),
        'items_list_navigation' => __('Daily Logs list navigation', 'habit-tracker'),
        'filter_items_list'     => __('Filter items list', 'habit-tracker'),
    );
    $args = array(
        'label'                 => __('Daily Log', 'habit-tracker'),
        'description'           => __('A post type for daily logging of habits', 'habit-tracker'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-calendar-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );

    register_post_type('daily_log', $args);
}
add_action('init', 'ht_register_daily_log_cpt', 0);

// Filter for single template
function ht_custom_single_template($single_template) {
    global $post;

    if ($post->post_type == 'habit') {
        $single_template = dirname(__FILE__) . '/templates/single-habit.php';
    } elseif ($post->post_type == 'daily_log') {
        $single_template = dirname(__FILE__) . '/templates/single-daily-log.php';
    }

    return $single_template;
}
add_filter('single_template', 'ht_custom_single_template');

// Filter for archive template
function ht_custom_archive_template($archive_template) {
    global $post;

    if (is_post_type_archive('habit')) {
        $archive_template = dirname(__FILE__) . '/templates/archive-habit.php';
    } elseif (is_post_type_archive('daily_log')) {
        $archive_template = dirname(__FILE__) . '/templates/archive-daily-log.php';
    }

    return $archive_template;
}
add_filter('archive_template', 'ht_custom_archive_template');


function habit_tracker_enqueue_styles() {
    global $post;

    // Check if the current page is a single habit, a single daily log, or their respective archives
    if (is_singular('habit') || is_singular('daily_log') || is_post_type_archive('habit') || is_post_type_archive('daily_log')) {
        // Use plugins_url to get the correct path to your CSS file
        $css_file_url = plugins_url('habit-tracker-styles.css', __FILE__);
        
        // Enqueue your stylesheet
        wp_enqueue_style('habit-tracker-styles', $css_file_url);
    }
}
add_action('wp_enqueue_scripts', 'habit_tracker_enqueue_styles');


function ht_activate() {
    ht_register_habit_cpt();
    ht_register_daily_log_cpt();
    flush_rewrite_rules(); // Flush rewrite rules to ensure CPTs are recognized
}
register_activation_hook(__FILE__, 'ht_activate');

function ht_deactivate() {
    flush_rewrite_rules(); // Clean up rewrite rules on deactivation
}
register_deactivation_hook(__FILE__, 'ht_deactivate');

