var
    gulp         = require('gulp'),
    newer        = require('gulp-newer'),
    imagemin     = require('gulp-imagemin'),
    htmlclean    = require('gulp-htmlclean'),
    sass         = require('gulp-sass'),
    postcss      = require('gulp-postcss'),
    assets       = require('postcss-assets'),
    autoprefixer = require('gulp-autoprefixer'),
    cssnano      = require('gulp-cssnano'),
    mqpacker     = require('css-mqpacker'),
    smushit      = require('gulp-smushit'),
    browser_sync = require('browser-sync').create(),

    // development mode?
    devBuild     = (process.env.NODE_ENV !== 'production'),

    folder       = {
        src:   'public/src/',
        build: 'public/build/',
    };

/**
 * Image processing task
 */
gulp.task('images', function () {
    let out = folder.build + 'img/';
    return gulp.src(folder.src + 'img/**/*')
        .pipe(imagemin({
            optimizationLevel: 10
        }))
        .pipe(gulp.dest(out));
});

/**
 * CSS processing task
 */
gulp.task('css', ['images'], function () {

    var postCssOpts = [
        assets({
            loadPaths: ['images/']
        }),
        autoprefixer({
            browsers: ['last 2 versions', '> 2%']
        }),
    ];

    if (!devBuild) {
        postCssOpts.push(cssnano);
    }

    return gulp.src(folder.src + 'scss/*.scss')
        .pipe(sass({
            outputStyle    : 'nested',
            imagePath      : 'img/',
            precision      : 3,
            errLogToConsole: true,
        }))
        //.pipe(postcss(postCssOpts))
        .pipe(cssnano())
        .pipe(gulp.dest(folder.build + 'css/'))
        .pipe(browser_sync.stream());

});

/**
 * Gulp watch listener
 */
gulp.task('watch', function () {
    browser_sync.init({
        files: [
            'public/index.html',
            'public/about.html',
            'public/contact.html',
        ],
        server: {
            baseDir: './public/',
        },
    });

    gulp.watch(folder.src + 'scss/**/*', ['css']);
    gulp.watch(folder.src + 'img/**/*', ['images']);

    gulp.watch('public/*.html')
        .on('change', browser_sync.reload);
});