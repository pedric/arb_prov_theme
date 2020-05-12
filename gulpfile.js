// const { series } = require('gulp');
const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const files = { 
  scssPath: 'src/scss/**/*.scss',
  jsPath: 'src/js/**/*.js'
}

function css() {
  return src(files.scssPath)
        .pipe(sass())
        .pipe(dest('css')
    )
}

function javascripts() {
  return src([
    files.jsPath
    ])
      .pipe(concat('main.js'))
      .pipe(uglify())
      .pipe(dest('js')
    )
}

function watchTask(){
    watch([files.scssPath, files.jsPath],
        {interval: 1000, usePolling: true},
        series(
            parallel(css, javascripts)
        )
    );    
}

exports.default = series(css, javascripts, watchTask);