// const { series } = require('gulp');
const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const vueify = require('gulp-vueify');
const files = { 
  scssPath: 'src/scss/main.scss',
  jsPath: 'src/js/**/*.js',
  vuePath: 'src/vue/**/*.vue'
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

function vue() {
  return src(files.vuePath)
    .pipe(vueify())
    .pipe(dest('vue'));
}

exports.default = series(css, javascripts, vue);