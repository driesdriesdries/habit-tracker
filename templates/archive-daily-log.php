<?php
/**
 * The template for displaying archive pages for 'daily_log' custom post type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Andries
 */

get_header();
?>

<main id="primary" class="site-main fade-in">

    <div class="wrapper single-post">
        <h1>Archive Daily Log</h1>
        
        <?php if ( have_posts() ) : ?>
            <ul>
                <?php while ( have_posts() ) : the_post(); ?>
                    <li>
                        <h2><?php the_title(); ?></h2> <!-- Display the daily log title -->
                        <p><strong>Date:</strong> <?php echo get_field('log_date'); ?></p>
                        <p><strong>Linked Habits:</strong>
                            <?php 
                            $linked_habits = get_field('linked_habits');
                            if( $linked_habits ):
                                echo '<ul>';
                                foreach( $linked_habits as $linked_habit ):
                                    echo '<li>' . get_the_title($linked_habit->ID) . '</li>';
                                endforeach;
                                echo '</ul>';
                            else:
                                echo 'None';
                            endif;
                            ?>
                        </p>
                        <p><strong>Completion Status:</strong> <?php echo get_field('completion_status') ? 'Completed' : 'Not Completed'; ?></p>
                        <p><strong>Notes:</strong> <?php echo get_field('notes') ? get_field('notes') : 'No additional notes.'; ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>No daily logs found.</p>
        <?php endif; ?>
        
    </div>  

</main><!-- #main -->

<?php
get_footer();
