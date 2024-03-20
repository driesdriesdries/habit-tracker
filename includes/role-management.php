<?php 
function ht_create_habit_tracker_subscriber_role() {
    // Define the capabilities for the Habit Tracker Subscriber role.
    $capabilities = array(
        'read' => true, // Allows a user to read posts.
        'edit_posts' => true, // Allows a user to edit their own posts.
        'edit_published_posts' => true, // Allows a user to edit their own published posts.
        'publish_posts' => true, // Allows a user to publish posts.
        'delete_posts' => true, // Allows a user to delete their own posts.
        'delete_published_posts' => true, // Allows a user to delete their own published posts.
        'upload_files' => true, // Allows a user to upload files.
        // Explicitly deny capabilities for managing other's posts or access to settings not related to their content.
        'edit_others_posts' => false,
        'delete_others_posts' => false,
        'manage_categories' => false,
        'moderate_comments' => false,
        'manage_links' => false,
        'edit_dashboard' => false,
        'edit_theme_options' => false,
        'update_core' => false,
        'update_plugins' => false,
        'update_themes' => false,
        'install_plugins' => false,
        'install_themes' => false,
        'delete_plugins' => false,
        'delete_themes' => false,
        'edit_plugins' => false,
        'edit_themes' => false,
        'edit_files' => false,
        'edit_users' => false,
        'create_users' => false,
        'delete_users' => false,
        'unfiltered_html' => false
    );

    // Add the Habit Tracker Subscriber role with defined capabilities.
    add_role('habit_tracker_subscriber', 'Habit Tracker Subscriber', $capabilities);
}

// Hook the function to run when the plugin is activated.
register_activation_hook(__FILE__, 'ht_create_habit_tracker_subscriber_role');

// Optional: Hook into plugin deactivation if you want to remove the role when your plugin is deactivated.
function ht_remove_habit_tracker_subscriber_role() {
    remove_role('habit_tracker_subscriber');
}
register_deactivation_hook(__FILE__, 'ht_remove_habit_tracker_subscriber_role');
?>