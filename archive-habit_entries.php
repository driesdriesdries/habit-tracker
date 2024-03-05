<?php
/**
 * The template for displaying archive pages for habit entries.
 * Includes a filter for displaying entries by year.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package YourThemeName
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header(); ?>

<div class="habit-archive-container">
	<div class="left">
	<?php 

		// Link to the admin area for habit entries
		$admin_habit_entries_url = admin_url('edit.php?post_type=habit_entries');
		?>

		<h1><a href="<?php echo esc_url($admin_habit_entries_url); ?>">Habit Performance</a></h1>

		<!-- Year filter form -->
		<form action="<?php echo esc_url(site_url('/habit-entries/')); ?>" method="get">
			<select name="year_filter">
				<option value="">Select a Year</option>
				<?php
				$current_year = date('Y');
				for ($year = $current_year; $year >= $current_year - 50; $year--) {
					echo '<option value="' . $year . '"' . (isset($_GET['year_filter']) && $_GET['year_filter'] == $year ? ' selected="selected"' : '') . '>' . $year . '</option>';
				}
				?>
			</select>
			<input type="submit" value="Filter">
		</form>

		<style>
			table {
				border-collapse: collapse;
				width: 100%;
			}
			th, td {
				border: 1px solid black;
				text-align: left;
				padding: 8px;
			}
			.yes {
				background-color: #90ee90; /* Light green */
			}
			.no {
				background-color: #f08080; /* Light red */
			}
		</style>

		<?php
		$args = [
			'post_type'      => 'habit_entries',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if (isset($_GET['year_filter']) && !empty($_GET['year_filter'])) {
			$args['date_query'] = [
				[
					'year' => $_GET['year_filter'],
				],
			];
		}

		$habit_query = new WP_Query($args);

		if ($habit_query->have_posts()) {
			echo '<table>';
			echo '<thead>';
			echo '<tr><th>Date</th>';

			// Fetch all ACF fields associated with the post type
			$field_groups = acf_get_field_groups(array('post_type' => 'habit_entries'));
			$field_keys = [];
			foreach ($field_groups as $field_group) {
				$fields = acf_get_fields($field_group['key']);
				foreach ($fields as $field) {
					// Only display true/false fields
					if ($field['type'] === 'true_false') {
						$field_keys[$field['name']] = $field['label'];
						echo '<th>' . esc_html($field['label']) . '</th>';
					}
				}
			}

			echo '</tr></thead><tbody>';

			while ($habit_query->have_posts()) {
				$habit_query->the_post();
				$edit_link = get_edit_post_link(); // This gets the backend edit link
				$date = get_the_date();
			
				echo "<tr><td><a href='$edit_link'>$date</a></td>";
			
				foreach ($field_keys as $key => $label) {
					$value = get_field($key);
					$class = $value ? 'yes' : 'no';
					$status = $value ? 'Yes' : 'No';
					echo "<td class='$class'>$status</td>";
				}
			
				echo '</tr>';
			}

			echo '</tbody></table>';
		} else {
			echo 'No habit entries found.';
		}

		wp_reset_postdata();
		?>
	</div>
	<div class="right">
		<h1 style="margin-bottom:4rem;">Here is a test Chart heading visualising my strongest habits</h1>
		<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<canvas id="habitChart" width="400" height="400"></canvas>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('habitChart').getContext('2d');
    var habitChart = new Chart(ctx, {
        type: 'polarArea', // Chart type set to polarArea
        data: {
            labels: [<?php 
                // Use the labels instead of the keys
                $labels = array_values($field_keys); // Assuming $field_keys' values are the friendly names
                echo '"' . implode('","', $labels) . '"';
             ?>],
            datasets: [{
                label: 'Yes Count',
                data: [<?php
                // Initialize an array to hold the counts
                $yesCounts = array_fill_keys(array_keys($field_keys), 0);

                // Reset post data to loop again for counts
                $habit_query->rewind_posts();

                while ($habit_query->have_posts()) {
                    $habit_query->the_post();
                    foreach ($field_keys as $key => $label) {
                        $value = get_field($key);
                        if ($value) {
                            $yesCounts[$key]++;
                        }
                    }
                }

                echo implode(',', $yesCounts); // Output counts as comma-separated values
                ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

	</div>
</div>




<?php get_footer(); ?>
