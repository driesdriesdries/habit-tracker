<?php 
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
?>