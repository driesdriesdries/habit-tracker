<?php
/**
 * Plugin Name: Habit Tracker
 * Plugin URI: http://yourwebsite.com/habit-tracker
 * Description: A plugin to track and manage habits.
 * Version: 1.1.1
 * Author: Andries Bester
 * Author URI: https://andriesbester.com/
 */
define('HABIT_TRACKER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HABIT_TRACKER_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path( __FILE__ ) . 'includes/role-management.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/custom-post-types.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-columns.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/template-filters.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/scripts-and-styles.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-customization.php';

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

