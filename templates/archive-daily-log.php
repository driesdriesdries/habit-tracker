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
        // First, fetch all habits to create table headers and gather goals and completion counts
        $habits_query = new WP_Query(array(
            'post_type' => 'habit',
            'posts_per_page' => -1, // Fetch all habits
        ));

        $habits = [];
        $habit_goals = [];
        $habit_completions = [];
        if ($habits_query->have_posts()) : 
            while ($habits_query->have_posts()) : $habits_query->the_post();
                $habit_id = get_the_ID();
                $habits[$habit_id] = get_the_title(); // Store habit ID and title
                $habit_goals[$habit_id] = get_field('goal_amount'); // Store habit goal amount
                $habit_completions[$habit_id] = 0; // Initialize completion count
            endwhile;
            wp_reset_postdata();
        endif;

        // Calculate completion counts
        $logs_query = new WP_Query(array(
            'post_type' => 'daily_log',
            'posts_per_page' => -1, // Fetch all logs
        ));
        if ($logs_query->have_posts()) :
            while ($logs_query->have_posts()) : $logs_query->the_post();
                $linked_habits = get_field('linked_habits');
                foreach ($linked_habits as $linked_habit) {
                    if (isset($habit_completions[$linked_habit->ID])) {
                        $habit_completions[$linked_habit->ID]++;
                    }
                }
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
                </tr>
                <tr>
                    <td>Goal</td>
                    <?php foreach ($habit_goals as $goal) : ?>
                        <td><?php echo esc_html($goal); ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td>Completed</td>
                    <?php foreach ($habit_completions as $completion) : ?>
                        <td><?php echo esc_html($completion); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php while (have_posts()) : the_post(); ?>
                    <tr>
                        <td><a href="<?php echo get_permalink(); ?>"><?php echo get_field('log_date'); ?></a></td>
                        <?php 
                        foreach ($habits as $habit_id => $habit_title) :
                            $linked_habits = get_field('linked_habits');
                            $is_completed = in_array($habit_id, array_map(function($habit) { return $habit->ID; }, (array) $linked_habits)) ? true : false;
                            ?>
                            <td class="<?php echo $is_completed ? 'green-cell' : 'red-cell'; ?>"><?php echo $is_completed ? '✔' : '✖'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No daily logs found.</p>
        <?php endif; ?>
    </div>  
</main><!-- #main -->

<?php get_footer(); ?>
