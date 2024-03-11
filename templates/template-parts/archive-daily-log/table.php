<?php
// Check if a specific year has been requested and sanitize the input
$filter_year = isset($_GET['filter_year']) ? intval($_GET['filter_year']) : date('Y');

$args = array(
    'post_type' => 'daily_log',
    'posts_per_page' => -1, // Get all posts
    'meta_query' => array(
        array(
            'key' => 'log_date',
            'value' => array($filter_year . '-01-01', $filter_year . '-12-31'),
            'compare' => 'BETWEEN',
            'type' => 'DATE'
        ),
    ),
    'orderby' => 'meta_value',
    'order' => 'DESC', // Order by date in descending order
);

// Get all daily_log posts for the selected year
$daily_logs = new WP_Query($args);

// Get all habit posts to create the headers
$habits = get_posts(array(
    'post_type' => 'habit',
    'posts_per_page' => -1, // Get all posts
));

// Define the symbols for "Yes" and "No"
$yes_symbol = '&#10003;'; // Checkmark
$no_symbol = '&#10007;'; // X
?>

<div class="daily-log-component">
    <!-- Year Filter Form -->
    <form action="<?php echo site_url('/daily_log/'); ?>" method="get">
        <label for="filter_year">Select Year:</label>
        <select id="filter_year" name="filter_year">
            <?php
            $current_year = date('Y');
            for ($year = $current_year; $year >= $current_year - 10; $year--) :
                ?>
                <option value="<?php echo $year; ?>" <?php echo isset($_GET['filter_year']) && $_GET['filter_year'] == $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
            <?php endfor; ?>
        </select>
        <input type="submit" value="Filter">
    </form>

    <?php if ($daily_logs->have_posts()) : ?>
        <!-- Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <?php foreach ($habits as $habit) : ?>
                            <th><?php echo esc_html($habit->post_title); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($daily_logs->have_posts()) : $daily_logs->the_post(); ?>
                        <tr>
                            <td><a href="<?php echo get_edit_post_link(); ?>"><?php echo esc_html(get_field('log_date')); ?></a></td>
                            <?php foreach ($habits as $habit) : ?>
                                <?php
                                $linked_habits = get_field('linked_habits');
                                $is_completed = in_array($habit->ID, array_map(function ($linked_habit) {
                                    return $linked_habit->ID;
                                }, $linked_habits)) ? true : false;
                                ?>
                                <td class="<?php echo $is_completed ? 'yes' : 'no'; ?>"><?php echo $is_completed ? $yes_symbol : $no_symbol; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>No daily logs found for the selected year.</p>
    <?php endif; ?>
</div>
