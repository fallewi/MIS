var gulp = require('gulp'),
    prefix = require('gulp-autoprefixer'),
    sass = require('gulp-sass'),
    livereload = require('gulp-livereload'),
    connect = require('gulp-connect'), //Сервер
    plumber = require('gulp-plumber'), //Слежение за ошибками
    notify = require('gulp-notify'),    //Нотайфер
    wiredep = require('wiredep').stream,
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant'),
    clean = require('gulp-clean'),
    zip = require('gulp-zip'),
    sourcemaps = require('gulp-sourcemaps'),
    // Variables
    destfolder = require('./package.json');

// Livereload connect
gulp.task('connect', function() {
  connect.server({
    root: '',
    livereload: true
  });
});

// Wiredep bower
gulp.task('bower', function () {
    gulp.src('./app/index.html')
        .pipe(wiredep({
          directory : "app/bower_components"
        }))
        .pipe(gulp.dest('./app'));
});

// SCSS
gulp.task('scss', function () {
    gulp.src('./scss/*.scss')
        .pipe(plumber({errorHandler: notify.onError({Error: '<%= error.message %>', sound : 'Bottle'}) }))
        // .pipe(sourcemaps.init())
          .pipe(sass())
        // .pipe(sourcemaps.write())
        .pipe(plumber.stop())
        .pipe(prefix({ browsers : ['last 5 versions', 'ie 8', 'ie 9'] }))
        .pipe(gulp.dest('./css'))
        .pipe(connect.reload())
        .pipe(notify({title : 'bourbon.css', icon : 'icon.png'}))
});

// Foundation SCSS
gulp.task('foundation', function() {
    gulp.src('./bower_components/foundation/scss/[^_]*.scss')
        .pipe(plumber({errorHandler: notify.onError({Error: '<%= error.message %>', sound : 'Bottle'}) }))
        .pipe(sass())
        .pipe(plumber.stop())
        .pipe(prefix({ browsers : ['last 5 versions', 'ie 8', 'ie 9'] }))
        .pipe(gulp.dest('./bower_components/foundation/css'))
        .pipe(connect.reload())
        .pipe(notify({title : 'Foundation.css', icon : 'icon.png'}))
    })

// Imagemin
gulp.task('imagemin', function () {
    return gulp.src('./img_source/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('./img/'))
        .pipe(notify({title : 'Imagemin'}))
});

// HTML
gulp.task('myreload', function() {
    gulp.src('./*.php')
        .pipe(connect.reload())
    });

// Watch     gulp.watch('./css/*.css', ['myreload'])
gulp.task('watch', function() {
    gulp.watch('./scss/**/*.scss', ['scss'])
    gulp.watch('./bower_components/foundation/scss/**/*.scss', ['foundation'])
    gulp.watch('./**/*.php', ['myreload'])
    gulp.watch('./style.css', ['myreload'])
    gulp.watch('./js/*.js', ['myreload'])
    gulp.watch('bower.json', ['bower'])
    gulp.watch('./img_source/*.*', ['imagemin'])
});

// Clear
gulp.task('clear',function(){
  return gulp.src(destfolder.name+'_'+destfolder.version+'/')
    .pipe(clean());
});

// Copy gulp.src(['./bower_components/**/*', '!./bower_components/{jquery*,jquery*/**}'])
gulp.task('copy', ['clear'], function() {
   return gulp.src(['**/*', '!{node_modules,node_modules/**,_main,_main/**,*.zip,*ftp*.json}'], {base: './'}, {read:false})
   .pipe(gulp.dest(destfolder.name+'_'+destfolder.version+'/'+destfolder.name+'_1/'+destfolder.name))
});

gulp.task('copymain', ['clear'], function() {
   return gulp.src(['_main/**/*'], {base: './_main'}, {read:false})
   .pipe(gulp.dest(destfolder.name+'_'+destfolder.version+'/'))
});

gulp.task('zipp', ['copy', 'copymain'], function() {
    return gulp.src(destfolder.name+'_'+destfolder.version+'/'+destfolder.name+'_1/**/*')
    .pipe(zip(destfolder.name+'_'+destfolder.version+'.zip'))
    .pipe(gulp.dest(destfolder.name+'_'+destfolder.version+'/'))
    .pipe(notify({title : 'Archive ready!', icon : 'icon.png', sound : 'Tink'}))
});

gulp.task('build', ['zipp']);

// Default
// gulp.task('default', ['connect', 'imagemin', 'myreload', 'scss', 'watch']);
gulp.task('default', ['connect', 'myreload', 'scss', 'watch']);
