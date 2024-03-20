<?php
function habit_tracker_enqueue_styles() {
    global $post;

    // Check if the current page is a single habit, a single daily log, or their respective archives
    if (is_singular('habit') || is_singular('daily_log') || is_post_type_archive('habit') || is_post_type_archive('daily_log')) {
        // Use the defined plugin URL constant to get the correct path to your CSS file
        $css_file_url = HABIT_TRACKER_PLUGIN_URL . 'habit-tracker-styles.css';
        
        // Enqueue your stylesheet
        wp_enqueue_style('habit-tracker-styles', $css_file_url);
    }
}
add_action('wp_enqueue_scripts', 'habit_tracker_enqueue_styles');

// Add Chart JS
function enqueue_chart_js() {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '2.9.4', true);
}
add_action('wp_enqueue_scripts', 'enqueue_chart_js');
?>