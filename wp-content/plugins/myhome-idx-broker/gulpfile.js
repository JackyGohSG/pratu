var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('default', function () {
    gulp.watch('assets/scss/**/**/**/*.scss', ['css']);
    gulp.task('css', function () {
        gulp.src('assets/scss/main.scss')
            .pipe(sass().on('error', sass.logError))
            .pipe(gulp.dest('assets/css/'))
    });
});
