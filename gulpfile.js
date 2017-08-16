var gulp = require('gulp');
var less = require('gulp-less');
var sass = require('gulp-sass');
var browserSync = require('browser-sync').create();
var header = require('gulp-header');
var cleanCSS = require('gulp-clean-css');
var rename = require("gulp-rename");
var uglify = require('gulp-uglify');
var pkg = require('./package.json');

// Set the banner content
var banner = ['/*!\n',
    ' * Start Bootstrap - <%= pkg.title %> v<%= pkg.version %> (<%= pkg.homepage %>)\n',
    ' * Copyright 2013-' + (new Date()).getFullYear(), ' <%= pkg.author %>\n',
    ' * Licensed under <%= pkg.license.type %> (<%= pkg.license.url %>)\n',
    ' */\n',
    ''
].join('');

// Compile LESS files from /less into /css
gulp.task('less', function () {
    return gulp.src('less/sb-admin-2.less')
            .pipe(less())
            .pipe(header(banner, {pkg: pkg}))
            .pipe(gulp.dest('dist/css'))
            .pipe(browserSync.reload({
                stream: true
            }))
});

// Compile SASS files
gulp.task('sass', function () {
    //compile scss
    gulp.src(['bower_components/datatables-responsive/css/*.scss'])
            .pipe(sass().on('error', sass.logError))
            .pipe(gulp.dest('bower_components/datatables-responsive/css'));
});

// Minify compiled CSS
gulp.task('minify-css', ['less'], function () {
    return gulp.src('dist/css/sb-admin-2.css')
            .pipe(cleanCSS({compatibility: 'ie8'}))
            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest('dist/css'))
            .pipe(browserSync.reload({
                stream: true
            }))
});

// Copy JS to dist
gulp.task('js', function () {
    return gulp.src(['js/sb-admin-2.js'])
            .pipe(header(banner, {pkg: pkg}))
            .pipe(gulp.dest('dist/js'))
            .pipe(browserSync.reload({
                stream: true
            }))
})

// Minify JS
gulp.task('minify-js', ['js'], function () {
    return gulp.src('js/sb-admin-2.js')
            .pipe(uglify())
            .pipe(header(banner, {pkg: pkg}))
            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest('dist/js'))
            .pipe(browserSync.reload({
                stream: true
            }))
});

// Copy vendor libraries from /bower_components into /vendor
gulp.task('copy', function () {
    gulp.src(['bower_components/bootstrap/dist/**/*', '!**/npm.js', '!**/bootstrap-theme.*', '!**/*.map'])
            .pipe(gulp.dest('vendor/bootstrap'))

    gulp.src(['bower_components/bootstrap-social/*.css', 'bower_components/bootstrap-social/*.less', 'bower_components/bootstrap-social/*.scss'])
            .pipe(gulp.dest('vendor/bootstrap-social'))

    gulp.src(['bower_components/datatables/media/**/*'])
            .pipe(gulp.dest('vendor/datatables'))

    gulp.src(['bower_components/bootbox.js/*.js'])
            .pipe(gulp.dest('vendor/bootbox'))

    gulp.src(['bower_components/datatables-plugins/integration/bootstrap/3/*'])
            .pipe(gulp.dest('vendor/datatables-plugins'))

    gulp.src(['bower_components/datatables-responsive/css/*.css', 'bower_components/datatables-responsive/js/*'])
            .pipe(gulp.dest('vendor/datatables-responsive'))
    
    gulp.src(['bower_components/bootstrap-wysiwyg/js/*'])
            .pipe(gulp.dest('vendor/bootstrap-wysiwyg'))
    
    gulp.src(['bower_components/underscore/*.js'])
            .pipe(gulp.dest('vendor/underscore'))
    
    gulp.src([
        'bower_components/bootstrap-calendar/*/*',
        ])
            .pipe(gulp.dest('vendor/bootstrap-calendar'))

    gulp.src(['bower_components/select2/dist/css/*', 'bower_components/select2/dist/js/*'])
            .pipe(gulp.dest('vendor/select2'))

    gulp.src(['bower_components/select2-bootstrap-theme/dist/*'])
            .pipe(gulp.dest('vendor/select2/themes'))

    gulp.src([
        'bower_components/fine-uploader/dist/jquery.fine-uploader.min.js',
        'bower_components/fine-uploader/dist/*.gif',
        'bower_components/fine-uploader/dist/placeholders/*.png',
        'bower_components/fine-uploader/dist/fine-uploader-new.min.css'
    ])
            .pipe(gulp.dest('vendor/fine-uploader'))

    gulp.src(['bower_components/moment/min/*'])
            .pipe(gulp.dest('vendor/moment'))

    gulp.src(['bower_components/jquery-validation/dist/*.min.js'])
            .pipe(gulp.dest('vendor/jquery-validation'))

    gulp.src(['bower_components/jquery-knob/dist/*'])
            .pipe(gulp.dest('vendor/jquery-knob'))

    gulp.src(['bower_components/eonasdan-bootstrap-datetimepicker/build/css/*', 'bower_components/eonasdan-bootstrap-datetimepicker/build/js/*'])
            .pipe(gulp.dest('vendor/eonasdan-bootstrap-datetimepicker'))

    gulp.src(['bower_components/flot/*.js'])
            .pipe(gulp.dest('vendor/flot'))

    gulp.src(['bower_components/flot.tooltip/js/*.js'])
            .pipe(gulp.dest('vendor/flot-tooltip'))

    gulp.src(['bower_components/font-awesome/**/*', '!bower_components/font-awesome/*.json', '!bower_components/font-awesome/.*'])
            .pipe(gulp.dest('vendor/font-awesome'))

    gulp.src(['bower_components/jquery/dist/jquery.js', 'bower_components/jquery/dist/jquery.min.js'])
            .pipe(gulp.dest('vendor/jquery'))

    gulp.src(['bower_components/metisMenu/dist/*'])
            .pipe(gulp.dest('vendor/metisMenu'))

    gulp.src(['bower_components/morrisjs/*.js', 'bower_components/morrisjs/*.css', '!bower_components/morrisjs/Gruntfile.js'])
            .pipe(gulp.dest('vendor/morrisjs'))

    gulp.src(['bower_components/raphael/raphael.js', 'bower_components/raphael/raphael.min.js'])
            .pipe(gulp.dest('vendor/raphael'))

})

// Run everything
gulp.task('default', ['minify-css', 'minify-js', 'copy']);

// Configure the browserSync task
gulp.task('browserSync', function () {
    browserSync.init({
        server: {
            baseDir: ''
        },
    })
})

// Dev task with browserSync
gulp.task('dev', ['browserSync', 'less', 'minify-css', 'js', 'minify-js'], function () {
    gulp.watch('less/*.less', ['less']);
    gulp.watch('dist/css/*.css', ['minify-css']);
    gulp.watch('js/*.js', ['minify-js']);
    // Reloads the browser whenever HTML or JS files change
    gulp.watch('pages/*.html', browserSync.reload);
    gulp.watch('dist/js/*.js', browserSync.reload);
});
