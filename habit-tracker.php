<?php
/**
 * Plugin Name: Habit Tracker
 * Plugin URI: http://yourwebsite.com/habit-tracker
 * Description: A plugin to track and manage habits.
 * Version: 1.0
 * Author: Andries Bester
 * Author URI: https://andriesbester.com/
 */

// Register Custom Post Type for Habit Entries
function register_habit_entries_post_type() {
    $args = array(
        'labels'             => array(
            'name'               => _x('Habit Entries', 'post type general name', 'your-plugin-textdomain'),
            'singular_name'      => _x('Habit Entry', 'post type singular name', 'your-plugin-textdomain'),
            'menu_name'          => _x('Habit Entries', 'admin menu', 'your-plugin-textdomain'),
            // Additional labels...
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'habit-entries'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-heart',
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
    );

    register_post_type('habit_entries', $args);
}
add_action('init', 'register_habit_entries_post_type');

// Add "View Performance" Button in Habit Edit Screen
function yourtheme_add_view_performance_button() {
    global $post;
    
    if ('habit_entries' === get_post_type($post->ID)) {
        $habit_archive_url = get_post_type_archive_link('habit_entries');
        
        if ($habit_archive_url) {
            echo '<div id="view-performance-action" class="misc-pub-section misc-pub-view-performance">';
            echo '<a href="' . esc_url($habit_archive_url) . '" class="button button-secondary" target="_blank">View Performance</a>';
            echo '</div>';
        }
    }
}
add_action('post_submitbox_misc_actions', 'yourtheme_add_view_performance_button');

// Load Custom Template for Habit Entries Archive
function my_plugin_load_custom_template( $template ) {
    if ( is_post_type_archive( 'habit_entries' ) ) {
        $custom_template = plugin_dir_path( __FILE__ ) . 'archive-habit_entries.php';
        if ( file_exists( $custom_template ) ) {
            return $custom_template;
        }
    }

    return $template;
}
add_filter( 'template_include', 'my_plugin_load_custom_template', 99 );

function habit_tracker_enqueue_styles() {
    // Use plugins_url to get the correct path to your CSS file
    $css_file_url = plugins_url('habit-tracker-styles.css', __FILE__);
    
    // Enqueue your stylesheet
    wp_enqueue_style('habit-tracker-styles', $css_file_url);
}
add_action('wp_enqueue_scripts', 'habit_tracker_enqueue_styles');
