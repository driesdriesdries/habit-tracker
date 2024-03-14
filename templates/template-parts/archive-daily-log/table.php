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

// Initialize an array to hold habit completion data
$habitCompletionData = array();

foreach ($habits as $habit) {
    // Fetch the goal amount for each habit
    $goal_amount = get_field('goal_amount', $habit->ID); // Ensure 'goal_amount' matches your actual field name
    
    $habitCompletionData[$habit->ID] = array(
        'title' => $habit->post_title,
        'goal_amount' => $goal_amount, // Add the goal amount here
        'completed_days' => 0,
        'total_days' => 0,
    );
}

// Update completed_days and total_days based on the daily logs
while ($daily_logs->have_posts()) {
    $daily_logs->the_post();
    $linked_habits = get_field('linked_habits');
    foreach ($linked_habits as $linked_habit) {
        if (isset($habitCompletionData[$linked_habit->ID])) {
            $habitCompletionData[$linked_habit->ID]['total_days']++;
            $is_completed = true; // Assuming it's completed for simplicity, adjust this based on your actual logic
            if ($is_completed) {
                $habitCompletionData[$linked_habit->ID]['completed_days']++;
            }
        }
    }
}

// Get the count of daily logs
$daily_logs_count = $daily_logs->post_count;

// Get the count of habits
$habits_count = count($habits);

// Calculate the total count of habit entries (daily logs multiplied by the amount of habits)
$total_habit_entries = $daily_logs_count * $habits_count;

// Initialize a variable to count completed habits
$total_completed_habits = 0;

// Update total_completed_habits based on the completion data
foreach ($habitCompletionData as $habitData) {
    $total_completed_habits += $habitData['completed_days'];
}

// Calculate the completion score (completed habits against total possible habits)
$completion_score = number_format($total_completed_habits / $total_habit_entries * 100, 2);

// Initialize an array to hold the strongest habits along with their completion percentages
$strongest_habits_with_percentage = array();

// Find the top 3 strongest habits based on completion percentage
$sorted_habits = $habitCompletionData; // Make a copy to avoid modifying the original array
usort($sorted_habits, function($a, $b) {
    // Compare completion percentage
    $percentage_a = $a['completed_days'] / $a['goal_amount'] * 100;
    $percentage_b = $b['completed_days'] / $b['goal_amount'] * 100;
    return $percentage_b - $percentage_a; // Sort in descending order
});

// Extract the top 3 strongest habits along with their completion percentages
for ($i = 0; $i < min(3, count($sorted_habits)); $i++) {
    $habit_title = $sorted_habits[$i]['title'];
    $completion_percentage = number_format(($sorted_habits[$i]['completed_days'] / $sorted_habits[$i]['goal_amount'] * 100), 2);
    $strongest_habits_with_percentage[] = $habit_title . ': ' . $completion_percentage . '%';
}

?>
<div class="daily-log-component">
    <div class="dash-panel">
        <form class="component" action="<?php echo site_url('/daily_log/'); ?>" method="get">
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
        
        <div class="panel-group component">
            <div class="panel">
                <h3>Habits Completed</h3>
                <p><span><?php echo $total_completed_habits; ?></span> / <span><?php echo $total_habit_entries; ?></span></p>
            </div>
            <div class="panel">
                <h3>Completion Score</h3>
                <p><?php echo $completion_score; ?>%</p>
            </div>
            <div class="panel">
                <h3>Strongest Habit(s)</h3>
                <?php 
                    // Echo out the <ul> of strongest habits with completion percentages
                    echo '<ul>';
                    foreach ($strongest_habits_with_percentage as $habit_info) {
                        echo '<li>' . $habit_info . '</li>';
                    }
                    echo '</ul>';
                ?>
            </div>
            <div class="panel">
                <h3>Weakest Habit(s)</h3>
                <p>66 / 323</p>
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

            // Populate data arrays
            Object.values(habits).forEach(function(habit) {
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
                backgroundColor: 'rgba(255, 215, 0, 0.5)', // Color: #FFD700
                backgroundColor: 'rgba(255, 215, 0, 0.5)', // Color: #FFD700
                borderWidth: 0
                }, {
                label: 'Completed Days',
                data: completedDays,
                backgroundColor: 'rgba(209, 231, 221, 1)', // Color: #d1e7dd
                backgroundColor: 'rgba(209, 231, 221, 1)', // Color: #d1e7dd
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
        <p class="component">No daily logs found for the selected year.</p>
    <?php endif; ?>

</div>
