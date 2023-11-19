var gulp = require("gulp");

// Requires the gulp-sass plugin
const sass = require("gulp-sass")(require("sass"));

var postcss = require("gulp-postcss");

const minifycss = require("gulp-uglifycss"); // Minifies CSS files.
const rename = require("gulp-rename"); // Renames files E.g. style.css -> style.min.css.

// Path to main .scss file.
const styleSRC = "./assets/sass/styles.scss";

// Path to place the compiled CSS file. Default set to root folder.
const styleDestination = "./assets/css/";

// Path to all *.scss files inside css folder and inside them.
const watchStyles = "./assets/sass/*.scss";

/**
 * Create css file and also a minified file
 */
gulp.task("styles", () => {
    var tailwindcss = require("tailwindcss");

    return gulp.src(styleSRC)
        .pipe(sass().on("error", sass.logError)) // Converts Sass to CSS with gulp-sass
        .pipe(
            postcss(
                [tailwindcss("./tailwind.config.js"), require("autoprefixer")]
            )
        )
        .pipe(gulp.dest(styleDestination))
        .pipe(rename({ suffix: ".min" }))
        .pipe(minifycss({ maxLineLen: 10 }))
        .pipe(gulp.dest(styleDestination));
});

/**
 * Watch Tasks. [ gulp / npm run start ]
 *
 * Watches for file changes and runs specific tasks.
 */
gulp.task(
    "default",
    gulp.parallel("styles", () => {
        gulp.watch(watchStyles, gulp.parallel("styles")); // Reload on SCSS file changes.
    })
);
