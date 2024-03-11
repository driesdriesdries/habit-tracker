<?php
/**
 * The template for displaying all single posts of the 'habit' type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Andries
 */

get_header(); ?>

<main id="primary" class="site-main fade-in">

    <div class="wrapper single-post">
        <?php while ( have_posts() ) : the_post(); ?>
            <h1><?php the_title(); ?></h1> <!-- Habit Title -->
            <p><strong>Description:</strong> <?php echo esc_html(get_field('habit_description')); ?></p>
            <p><strong>Category:</strong> <?php echo esc_html(get_field('category')); ?></p>
            <p><strong>Frequency:</strong> <?php echo esc_html(get_field('frequency')); ?></p>
            <p><strong>Priority:</strong> <?php echo esc_html(get_field('priority')); ?></p>
            <p><strong>Start Date:</strong> <?php echo esc_html(get_field('start_date')); ?></p>
            <p><strong>Goal Amount:</strong> <?php echo esc_html(get_field('goal_amount')); ?></p>
            
        <?php endwhile; // End of the loop. ?>
    </div>  

</main><!-- #main -->

<?php
get_footer();
