<?php
/**
 * The template for displaying all single posts of the 'daily_log' type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Andries
 */

get_header(); ?>

<main id="primary" class="site-main fade-in">

    <div class="wrapper single-post">
        <?php while ( have_posts() ) : the_post(); ?>
            <h1><?php the_title(); ?></h1> <!-- Daily Log Title -->
            <p><strong>Date:</strong> <?php echo get_field('log_date'); ?></p>
            
            <p><strong>Linked Habits:</strong></p>
            <?php 
            $linked_habits = get_field('linked_habits');
            if( $linked_habits ): ?>
                <ul>
                    <?php foreach( $linked_habits as $linked_habit ): ?>
                        <li><?php echo get_the_title( $linked_habit->ID ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No habits linked.</p>
            <?php endif; ?>
            
            <p><strong>Completion Status:</strong> <?php echo get_field('completion_status') ? 'Completed' : 'Not Completed'; ?></p>
            <p><strong>Notes:</strong> <?php echo get_field('notes') ? get_field('notes') : 'No additional notes.'; ?></p>
            
        <?php endwhile; // End of the loop. ?>
    </div>  

</main><!-- #main -->

<?php
get_footer();
?>
