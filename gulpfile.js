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
    browser_sync = require('browser-sync'),

    concat       = require('gulp-concat'),
    deporder     = require('gulp-deporder'),
    stripdebug   = require('gulp-strip-debug'),
    uglify       = require('gulp-uglify'),

    php          = require('gulp-connect-php'),

    // development mode?
    devBuild     = (process.env.NODE_ENV !== 'PRODUCTION'),

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
 * JS processing task
 */
gulp.task('js', function () {
    let jsbuild = gulp.src(folder.src + 'js/**/*')
        .pipe(deporder());

    if (!devBuild) {
        jsbuild = jsbuild
            .pipe(stripdebug())
            .pipe(uglify());
    }

    return jsbuild.pipe(gulp.dest(folder.build + 'js/'));
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

gulp.task('twig', function () {
    let templates = gulp.src(folder.src + 'views/**/*');

    return templates.pipe(gulp.dest(folder.build + 'views/'));
});

/**
 * PHP server task
 */
gulp.task('php', function () {
    php.server({
        base     : 'public',
        port     : 8010,
        keepalive: true,
    });
});

/**
 * BrowserSync task
 */
gulp.task('browser-sync', ['php'], function () {
    const routes = {
        '/about'       : '/about.php',
        '/credits'     : '/credits.php',
        '/media'       : '/media.php',
        '/compositions': '/compositions.php',
        '/contact'     : '/contact.php',
    };

    browser_sync({
        proxy : {
            target: '127.0.0.1:8010',
            proxyReq: [
                (proxyReq) => {
                    if (proxyReq.path in routes) {
                        proxyReq.path = routes[proxyReq.path];
                    }
                }
            ],
        },
        port  : 8080,
        open  : true,
        notify: false,
    });
});

/**
 * Build task
 */
gulp.task('build', ['js', 'css', 'images', 'twig']);

/**
 * Gulp watch listener
 */
gulp.task('watch', ['browser-sync'], function () {
    gulp.watch(folder.src + 'js/**/*', ['js']);
    gulp.watch(folder.src + 'scss/**/*', ['css']);
    gulp.watch(folder.src + 'img/**/*', ['images']);
    gulp.watch(['public/php/*.php', 'php/*.php'], browser_sync.reload);
    gulp.watch(folder.src + 'views/**/*', ['twig']);

    gulp.watch('public/*.html')
        .on('change', browser_sync.reload);
});