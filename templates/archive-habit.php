<?php
/**
 * The template for displaying archive pages for 'habit' custom post type
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Andries
 */

get_header();
?>

<main id="primary" class="site-main fade-in">

    <div class="wrapper single-post">
        
        <h1>Habits</h1>
        
        <?php if ( have_posts() ) : ?>
            <ul>
                <?php while ( have_posts() ) : the_post(); ?>
                    <li>
                        <h2><?php the_title(); ?></h2> <!-- Display the habit title -->
                        <p><strong>Description:</strong> <?php echo get_field('habit_description'); ?></p>
                        <p><strong>Category:</strong> <?php echo get_field('category'); ?></p>
                        <p><strong>Frequency:</strong> <?php echo get_field('frequency'); ?></p>
                        <p><strong>Priority:</strong> <?php echo get_field('priority'); ?></p>
                        <p><strong>Start Date:</strong> <?php echo get_field('start_date') ? get_field('start_date') : 'Not specified'; ?></p>
                        <p><strong>Goal Amount:</strong> <?php echo get_field('goal_amount') ? get_field('goal_amount') : 'Not specified'; ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>No habits found.</p>
        <?php endif; ?>
        
    </div>  

</main><!-- #main -->

<?php
get_footer();
