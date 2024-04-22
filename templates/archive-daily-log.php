<?php
if (is_user_logged_in()) {
    get_header();
?>

<div class="plugin-container">
    <?php
    $current_year = date('Y');
    $current_date = date('Y-m-d');
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : $current_year . '-01-01';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : $current_date;
    $filter_year = isset($_GET['filter_year']) ? intval($_GET['filter_year']) : $current_year;
    $args = array(
        'post_type' => 'daily_log',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'log_date',
                'value' => array($filter_year . '-01-01', $filter_year . '-12-31'),
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            ),
            array(
                'key' => 'log_date',
                'value' => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            )
        )
    );
    $daily_logs = new WP_Query($args);
    $habits = get_posts(array(
        'post_type' => 'habit',
        'posts_per_page' => -1,
    ));
    $yes_symbol = '&#10003;';
    $no_symbol = '&#10007;';
    $habitCompletionData = array();
    foreach ($habits as $habit) {
        $goal_amount = get_field('goal_amount', $habit->ID);
        $habitCompletionData[$habit->ID] = array(
            'title' => $habit->post_title,
            'goal_amount' => $goal_amount,
            'completed_days' => 0,
            'total_days' => 0,
        );
    }
    while ($daily_logs->have_posts()) {
        $daily_logs->the_post();
        $linked_habits = get_field('linked_habits');
        foreach ($linked_habits as $linked_habit) {
            if (isset($habitCompletionData[$linked_habit->ID])) {
                $habitCompletionData[$linked_habit->ID]['total_days']++;
                $is_completed = true;
                if ($is_completed) {
                    $habitCompletionData[$linked_habit->ID]['completed_days']++;
                }
            }
        }
    }
    $daily_logs_count = $daily_logs->post_count;
    $habits_count = count($habits);
    $total_habit_entries = $daily_logs_count * $habits_count;
    $total_completed_habits = 0;
    foreach ($habitCompletionData as $habitData) {
        $total_completed_habits += $habitData['completed_days'];
    }
    $completion_score = number_format($total_completed_habits / $total_habit_entries * 100, 2);
    $habit_progress_with_percentage = array();
    $sorted_habits = $habitCompletionData;
    usort($sorted_habits, function ($a, $b) {
        return ($b['completed_days'] / max($b['goal_amount'], 1)) <=> ($a['completed_days'] / max($a['goal_amount'], 1));
    });
    for ($i = 0; $i < min(5, count($sorted_habits)); $i++) {
        $completion_percentage = number_format(($sorted_habits[$i]['completed_days'] / max($sorted_habits[$i]['goal_amount'], 1) * 100), 2);
        $habit_progress_with_percentage[] = $sorted_habits[$i]['title'] . ': ' . $completion_percentage . '%';
    }
    $weakestHabits = array_slice($sorted_habits, -5);
    $weakestHabitsList = '<ul>';
    foreach ($weakestHabits as $habit) {
        $completionRate = number_format(($habit['completed_days'] / max($habit['goal_amount'], 1)) * 100, 2);
        $weakestHabitsList .= '<li>' . $habit['title'] . ' - ' . $completionRate . '%</li>';
    }
    $weakestHabitsList .= '</ul>';

    function calculate_top_three_streaks($start_date, $end_date) {
        // Fetch all habits
        $habits = get_posts([
            'post_type' => 'habit',
            'posts_per_page' => -1,
        ]);
    
        // If no habits are found, return an empty array to indicate no streak data
        if (empty($habits)) {
            return []; 
        }
    
        $streaks = [];
    
        foreach ($habits as $habit) {
            // Fetch daily logs associated with each habit within the specified date range
            $daily_logs = get_posts([
                'post_type' => 'daily_log',
                'posts_per_page' => -1,
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key' => 'linked_habits',
                        'value' => '"' . $habit->ID . '"',
                        'compare' => 'LIKE',
                    ],
                    [
                        'key' => 'log_date',
                        'value' => [$start_date, $end_date],
                        'compare' => 'BETWEEN',
                        'type' => 'DATE',
                    ],
                ],
                'orderby' => 'meta_value',
                'meta_key' => 'log_date',
                'order' => 'ASC',
            ]);
    
            // Initialize streak calculation variables
            $currentStreak = 0;
            $longestStreak = 0;
            $previousDate = null;
    
            // Iterate through each daily log to calculate the current and longest streaks
            foreach ($daily_logs as $log) {
                $logDate = get_field('log_date', $log->ID);
                $logDateTimestamp = strtotime($logDate);
    
                // Increment current streak if the log is consecutive; otherwise, reset it
                if ($previousDate === null || ($logDateTimestamp - $previousDate) === DAY_IN_SECONDS) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1; // Reset streak if there's a gap
                }
    
                // Update longest streak if current streak exceeds it
                if ($currentStreak > $longestStreak) {
                    $longestStreak = $currentStreak;
                }
    
                $previousDate = $logDateTimestamp;
            }
    
            // Store the longest streak for the current habit
            $streaks[$habit->post_title] = $longestStreak;
        }
    
        // Sort the streaks in descending order to find the top three
        arsort($streaks);
        $topThreeStreaks = array_slice($streaks, 0, 3, true);
    
        return $topThreeStreaks;
    }
    
    
    ?>
    <div class="daily-log-component">
        <div class="dash-panel">
            <form class="component" action="<?php echo site_url('/daily_log/'); ?>" method="get">
                <div class="filter-date-selection">
                    <div class="start-date">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="end-date">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                </div>
                <input type="submit" value="Filter">
            </form>
            <div class="panel-group component">
                <div class="panel">
                    <h3>Habits Completed</h3>
                    <p><span><?php echo $total_completed_habits; ?></span> / <span><?php echo $total_habit_entries; ?></span> <?php echo '(' . $completion_score . ')'; ?>%</p>
                    <?php 
                        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : $current_year . '-01-01';
                        $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : $current_date;
                        
                        // Fetch the top three streaks within the given date range
                        $topThreeStreaks = calculate_top_three_streaks($start_date, $end_date);

                        // Check if there are any streaks to display
                        if (!empty($topThreeStreaks)) {
                            echo '<h3>Streak Information</h3>';
                            echo '<ul>';
                            foreach ($topThreeStreaks as $habitName => $streakLength) {
                                echo '<li>' . esc_html($habitName) . ': <span>' . $streakLength . ' days</span></li>';
                            }
                            echo '</ul>';
                        } else {
                            // If no streaks are found, inform the user
                            echo '<h3>Streak Information</h3>';
                            echo '<p>No streak information available.</p>';
                        }
                    ?>
                </div>
                <div class="panel">
                    <h3>Progress Toward Goal</h3>
                    <?php
                    echo '<ul>';
                    foreach ($habit_progress_with_percentage as $habit_info) {
                        echo '<li>' . $habit_info . '</li>';
                    }
                    echo '</ul>';
                    ?>
                </div>
                <div class="panel">
                    <h3>Weakest Habit(s)</h3>
                    <?php
                    echo $weakestHabitsList;
                    ?>
                </div>
            </div>
        </div>
        <div class="graph component">
            <canvas id="habitGraph" width="400" height="200"></canvas>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var ctx = document.getElementById('habitGraph').getContext('2d');
                    var habits = <?php echo json_encode($habitCompletionData); ?>;
                    var habitLabels = [];
                    var goalAmounts = [];
                    var completedDays = [];
                    Object.values(habits).forEach(function (habit) {
                        habitLabels.push(habit.title);
                        goalAmounts.push(habit.goal_amount);
                        completedDays.push(habit.completed_days);
                    });
                    var chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: habitLabels,
                            datasets: [{
                                label: 'Goal Amount',
                                data: goalAmounts,
                                backgroundColor: 'rgb(239, 239, 239)',
                                borderWidth: 0
                            }, {
                                label: 'Completed Days',
                                data: completedDays,
                                backgroundColor: 'rgba(209, 231, 221, 1)',
                                borderWidth: 0
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });
                });
            </script>
        </div>
        <?php if ($daily_logs->have_posts()) : ?>
            <div class="table-wrapper component">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <?php foreach ($habits as $habit) : ?>
                                <th><a href="<?php echo get_edit_post_link($habit->ID); ?>">
                                        <?php echo esc_html($habit->post_title); ?>
                                    </a></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($daily_logs->have_posts()) : $daily_logs->the_post(); ?>
                            <tr>
                            <td><a href="<?php echo get_edit_post_link(); ?>">
                                    <?php 
                                    $log_date = get_field('log_date'); // Store the log date in a variable
                                    echo esc_html($log_date) . ' (' . date('l', strtotime($log_date)) . ')'; // Display the date and day name
                                    ?>
                                </a></td>
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
            <p class="component">No daily logs found for the selected year.</p>
        <?php endif; ?>

    </div>

</div>

<?php
get_footer();
} else {
    wp_redirect(wp_login_url());
    exit;
}
?>
