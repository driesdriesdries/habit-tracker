<?php 
function ht_add_view_performance_button() {
    global $post;

    // Check if we're on the daily_log post type
    if ($post->post_type == 'daily_log') {
        // Get the URL to the daily log archive
        $archive_url = get_post_type_archive_link('daily_log');
        
        // Only add the button if we have a valid URL
        if ($archive_url) {
            // Inline CSS for the button
            $button_style = 'style="background-color: crimson; border-color: crimson; color: white; margin-top: 10px;"';

            // Echo the button with the custom style
            echo '<a href="' . esc_url($archive_url) . '" class="button button-primary" ' . $button_style . '>View Performance</a>';
        }
    }
}
add_action('edit_form_after_title', 'ht_add_view_performance_button');

function update_daily_log_title_with_date( $post_id ) {
    // Check if this is a 'daily_log' post type.
    if ( get_post_type( $post_id ) !== 'daily_log' ) {
        return;
    }

    // Check if the current user can edit the post.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Get the selected date from ACF field. Adjust the field key to match your setup.
    $date = get_field( 'log_date', $post_id );

    // Proceed if the date is not empty.
    if ( ! empty( $date ) ) {
        // Format the date to your preference. E.g., "F j, Y" turns "2023-03-18" into "March 18, 2023".
        $formatted_date = date( "F j, Y", strtotime( $date ) );

        // Prepare post object to update the title.
        $post_data = array(
            'ID'         => $post_id,
            'post_title' => $formatted_date, // Use the formatted date as the post title.
            // 'post_name'  => sanitize_title( $formatted_date ), // Optional: uncomment to update the slug as well.
        );

        // Temporarily unhook this function to prevent infinite loop.
        remove_action( 'save_post', 'update_daily_log_title_with_date' );

        // Update the post, which changes the title.
        wp_update_post( $post_data );

        // Re-hook this function.
        add_action( 'save_post', 'update_daily_log_title_with_date' );
    }
}

// Hook into save_post action.
add_action( 'save_post', 'update_daily_log_title_with_date' );

function remove_title_field_for_daily_logs() {
    remove_post_type_support( 'daily_log', 'title' );
}
add_action( 'admin_init', 'remove_title_field_for_daily_logs' );

?>