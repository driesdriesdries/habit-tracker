<?php
// Filter for single template
function ht_custom_single_template($single_template) {
    global $post;

    if ($post->post_type == 'habit') {
        $single_template = HABIT_TRACKER_PLUGIN_DIR . 'templates/single-habit.php';
    } elseif ($post->post_type == 'daily_log') {
        $single_template = HABIT_TRACKER_PLUGIN_DIR . 'templates/single-daily-log.php';
    }

    return $single_template;
}
add_filter('single_template', 'ht_custom_single_template');

// Filter for archive template
function ht_custom_archive_template($archive_template) {
    if (is_post_type_archive('habit')) {
        $archive_template = HABIT_TRACKER_PLUGIN_DIR . 'templates/archive-habit.php';
    } elseif (is_post_type_archive('daily_log')) {
        $archive_template = HABIT_TRACKER_PLUGIN_DIR . 'templates/archive-daily-log.php';
    }

    return $archive_template;
}
add_filter('archive_template', 'ht_custom_archive_template');

?>
