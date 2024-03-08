<?php
/**
 * The template for displaying archive pages for 'daily_log' custom post type,
 * with a simple year selection filter added for the 'log_date' custom field,
 * dynamic panels showing the total number of habits completed, and the strongest habit(s)
 * including handling ties, and displaying the weakest habit(s).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Andries
 */

get_header(); ?>

<?php if (is_user_logged_in()) : ?>
    <main id="primary" class="site-main fade-in">
        <div class="wrapper daily-log-archive">
            <div class="primary-container">
                <div class="panel-group">
                    <div class="panel">
                        <p>Total Habit Completed</p>
                        <span id="total_completions">Calculating...</span>
                    </div>
                    <div class="panel">
                        <p>Strongest Habit(s)</p>
                        <span id="strongest_habit">Calculating...</span>
                    </div>
                    <div class="panel">
                        <p>Weakest Habit(s)</p>
                        <span id="weakest_habit">Calculating...</span>
                    </div>
                    <div class="panel">
                        <p>Longest streak</p>
                        <span>xxx</span>
                    </div>
                </div>
                <div class="graph">
                    <h3>Graph</h3>
                    <canvas id="habitsChart" width="400" height="200"></canvas>
                </div>
                <div class="table">
                    <h3>All Habits Performance</h3>

                    <!-- Year filter form -->
                    <form action="<?php echo site_url('/daily_log/'); ?>" method="get">
                        <label for="filter_year">Select Year:</label>
                        <select id="filter_year" name="filter_year">
                            <?php
                            $current_year = date('Y');
                            for ($year = $current_year; $year >= $current_year - 10; $year--): ?>
                                <option value="<?php echo $year; ?>" <?php echo isset($_GET['filter_year']) && $_GET['filter_year'] == $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                            <?php endfor; ?>
                        </select>
                        <input type="submit" value="Filter">
                    </form>

                    <?php
                    // Adjusting the main query based on the selected year
                    $filter_year = $_GET['filter_year'] ?? date('Y');
                    $args = array(
                        'post_type' => 'daily_log',
                        'posts_per_page' => -1, // Fetch all logs
                        'meta_query' => array(
                            array(
                                'key' => 'log_date',
                                'value' => array($filter_year.'-01-01', $filter_year.'-12-31'),
                                'compare' => 'BETWEEN',
                                'type' => 'DATE'
                            ),
                        ),
                    );
                    $logs_query = new WP_Query($args);

                    // Fetch habits for chart and table headers
                    $habits_query = new WP_Query(array(
                        'post_type' => 'habit',
                        'posts_per_page' => -1, // Fetch all habits
                    ));

                    $habits = [];
                    $habit_goals = [];
                    $habit_completions = [];
                    $strongest_habits = []; // Store IDs of strongest habits
                    $weakest_habits = []; // Store IDs of weakest habits

                    if ($habits_query->have_posts()) :
                        while ($habits_query->have_posts()) : $habits_query->the_post();
                            $habit_id = get_the_ID();
                            $habits[$habit_id] = get_the_title();
                            $habit_goals[$habit_id] = get_field('goal_amount');
                            $habit_completions[$habit_id] = 0;
                        endwhile;
                        wp_reset_postdata();
                    endif;

                    if ($logs_query->have_posts()) :
                        while ($logs_query->have_posts()) : $logs_query->the_post();
                            $linked_habits = get_field('linked_habits');
                            foreach ($linked_habits as $linked_habit) {
                                if (isset($habit_completions[$linked_habit->ID])) {
                                    $habit_completions[$linked_habit->ID]++;
                                    // Check for tie or new strongest habit
                                    if ($habit_completions[$linked_habit->ID] > $strongest_habit_completions) {
                                        $strongest_habits = [$linked_habit->ID]; // New strongest habit found
                                        $strongest_habit_completions = $habit_completions[$linked_habit->ID];
                                    } elseif ($habit_completions[$linked_habit->ID] == $strongest_habit_completions) {
                                        $strongest_habits[] = $linked_habit->ID; // Add tied habit
                                    }
                                    // Check for tie or new weakest habit
                                    if ($habit_completions[$linked_habit->ID] < $weakest_habit_completions || !isset($weakest_habit_completions)) {
                                        $weakest_habits = [$linked_habit->ID]; // New weakest habit found
                                        $weakest_habit_completions = $habit_completions[$linked_habit->ID];
                                    } elseif ($habit_completions[$linked_habit->ID] == $weakest_habit_completions) {
                                        $weakest_habits[] = $linked_habit->ID; // Add tied habit
                                    }
                                }
                            }
                        endwhile;
                        wp_reset_postdata();
                    endif;

                    // Convert habit IDs to names for display
                    $strongest_habit_names = array_intersect_key($habits, array_flip($strongest_habits));
                    $weakest_habit_names = array_intersect_key($habits, array_flip($weakest_habits));
                    ?>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var ctx = document.getElementById('habitsChart').getContext('2d');
                            var habitsChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: <?php echo json_encode(array_values($habits)); ?>,
                                    datasets: [{
                                        label: 'Goals',
                                        data: <?php echo json_encode(array_values($habit_goals)); ?>,
                                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }, {
                                        label: 'Completions',
                                        data: <?php echo json_encode(array_values($habit_completions)); ?>,
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                            // Update total completions, strongest habit(s), and weakest habit(s) span with calculated totals and names
                            document.getElementById('total_completions').textContent = '<?php echo array_sum($habit_completions); ?>';
                            document.getElementById('strongest_habit').textContent = '<?php echo implode(', ', $strongest_habit_names); ?>';
                            document.getElementById('weakest_habit').textContent = '<?php echo implode(', ', $weakest_habit_names); ?>';
                        });
                    </script>

                    <?php if ($logs_query->have_posts()) : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <?php foreach ($habits as $habit_title) : ?>
                                        <th><?php echo esc_html($habit_title); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($logs_query->have_posts()) : $logs_query->the_post(); ?>
                                    <tr>
                                        <td><a href="<?php the_permalink(); ?>"><?php the_field('log_date'); ?></a></td>
                                        <?php
                                        foreach ($habits as $habit_id => $habit_title) :
                                            $linked_habits = get_field('linked_habits');
                                            $is_completed = in_array($habit_id, array_map(function ($habit) {
                                                return $habit->ID;
                                            }, (array)$linked_habits)) ? true : false;
                                            ?>
                                            <td class="<?php echo $is_completed ? 'green-cell' : 'red-cell'; ?>"><?php echo $is_completed ? '✔' : '✖'; ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>No daily logs found for the selected year.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main><!-- #main -->
<?php else : ?>
    <p>You must be logged in to view this content.</p>
<?php endif; ?>

<?php get_footer(); ?>
