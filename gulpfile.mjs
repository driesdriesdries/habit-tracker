// Import modules
import gulp from 'gulp';
import sass from 'gulp-sass';
import dartSass from 'sass';
import rename from 'gulp-rename'; // Make sure to install gulp-rename if you haven't

// Initialize sass compiler
const compileSass = sass(dartSass);

// Compile SCSS to CSS
function styles() {
    return gulp.src('sass/main.scss') // Source file
        .pipe(compileSass().on('error', compileSass.logError))
        .pipe(rename('habit-tracker-styles.css')) // Rename output file if needed
        .pipe(gulp.dest('./')); // Output directory
}

// Watch task
function watch() {
    gulp.watch('sass/**/*.scss', styles);
}

// Explicitly define watch as a task
gulp.task('watch', watch);

// Default task that runs styles and then watches
export default gulp.series(styles, watch);
