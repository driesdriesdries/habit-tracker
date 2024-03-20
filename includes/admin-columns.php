<?php 
// Insert the Author column in a specific position for Habits and Daily Logs
function ht_add_author_column_in_middle( $columns ) {
    // New column to be added
    $new_column = ['author_name' => __('Author', 'habit-tracker')];

    // Choose the position of the new column (e.g., after 'title')
    $after = 'title'; // This positions the new column after the "Title" column
    $position = array_search($after, array_keys($columns)) + 1;

    // Split the original columns into two parts
    $before = array_slice($columns, 0, $position, true);
    $after = array_slice($columns, $position, null, true);

    // Merge arrays to insert the new column
    $columns = array_merge($before, $new_column, $after);

    return $columns;
}
add_filter( 'manage_habit_posts_columns', 'ht_add_author_column_in_middle' );
add_filter( 'manage_daily_log_posts_columns', 'ht_add_author_column_in_middle' );

// Populate the Author column with clickable names that filter posts by author
// Modify to exclude clickable link for Habit Tracker Subscribers
function ht_show_author_name_in_column( $column, $post_id ) {
    if ( $column == 'author_name' ) {
        $author_id = get_post_field( 'post_author', $post_id );
        $author_name = get_the_author_meta( 'display_name', $author_id );

        // Check user role
        if (!current_user_can('edit_others_posts')) {
            // Just display the author's name for users who cannot edit other's posts (like Habit Tracker Subscribers)
            echo esc_html( $author_name );
        } else {
            // For users who can edit other's posts, make the author's name a clickable link
            $query_args = array(
                'post_type' => get_post_type( $post_id ),
                'author'    => $author_id,
            );
            $filter_url = add_query_arg( $query_args, admin_url( 'edit.php' ) );

            // Output the link
            echo '<a href="' . esc_url( $filter_url ) . '">' . esc_html( $author_name ) . '</a>';
        }
    }
}
add_action( 'manage_habit_posts_custom_column', 'ht_show_author_name_in_column', 10, 2 );
add_action( 'manage_daily_log_posts_custom_column', 'ht_show_author_name_in_column', 10, 2 );
?>