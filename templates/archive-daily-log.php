<?php
/**
 * The template for displaying archive pages for 'daily_log' custom post type.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Andries
 */

get_header(); ?>

<main id="primary" class="site-main fade-in">
    <div class="wrapper single-post daily-log-archive">
        <div class="primary-container">
            <div class="left">Chart will go here</div>
            <div class="right">Panels will go here</div>
        </div>
        <h1>Archive Daily Log</h1>

        <?php
        // First, fetch all habits to create table headers
        $habits_query = new WP_Query(array(
            'post_type' => 'habit',
            'posts_per_page' => -1, // Fetch all habits
        ));

        $habits = [];
        if ($habits_query->have_posts()) : 
            while ($habits_query->have_posts()) : $habits_query->the_post();
                $habits[get_the_ID()] = get_the_title(); // Store habit ID and title
            endwhile;
            wp_reset_postdata();
        endif;

        if (have_posts()) : ?>
            <table>
                <tr>
                    <th>Date</th>
                    <?php foreach ($habits as $habit_title) : ?>
                        <th><?php echo esc_html($habit_title); ?></th>
                    <?php endforeach; ?>
                    <th>Notes</th>
                </tr>
                <?php while (have_posts()) : the_post(); ?>
                    <tr>
                        <!-- Make the date clickable and point to the post permalink -->
                        <td><a href="<?php echo get_permalink(); ?>"><?php echo get_field('log_date'); ?></a></td>
                        <?php 
                        foreach ($habits as $habit_id => $habit_title) :
                            $linked_habits = get_field('linked_habits');
                            $is_completed = in_array($habit_id, array_map(function($habit) { return $habit->ID; }, (array) $linked_habits)) ? true : false; // Check if habit is linked and marked completed
                            ?>
                            <td class="<?php echo $is_completed ? 'green-cell' : 'red-cell'; ?>"><?php echo $is_completed ? '✔' : '✖'; ?></td>
                        <?php endforeach; ?>
                        <td><?php echo get_field('notes') ? esc_html(get_field('notes')) : 'No additional notes.'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No daily logs found.</p>
        <?php endif; ?>
    </div>  
</main><!-- #main -->

<?php get_footer(); ?>
