'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
sass.compiler = require('node-sass');

// Path to main .scss file.
const styleSRC = './assets/sass/styles.scss';
const sassWatch = './assets/sass/**/*.scss';
const styleDest = './assets/css'; // Destination Folder

gulp.task('styles', function () {
    return gulp.src(styleSRC)
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(gulp.dest(styleDest));
});

gulp.task('styles:watch', function () {
    gulp.watch(sassWatch, ['styles']);
});
