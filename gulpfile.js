const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')
const esbuild = require('gulp-esbuild')
const gulp = require('gulp')
const postcss = require('gulp-postcss')
const rename = require('gulp-rename')
const sass = require('gulp-sass')(require('sass'))

const styles = () => {
    return gulp.src('src/Resources/public/cokiban.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([
            autoprefixer(),
            cssnano({ preset: ['default', { discardComments: { removeAll: true } }] })
        ]))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('src/Resources/public/'))
}

const scripts = () => {
    return gulp.src('src/Resources/public/cokiban.js')
        .pipe(esbuild({
            outfile: 'cokiban.min.js',
            minify: true
        }))
        .pipe(gulp.dest('src/Resources/public/'))
}

const build = gulp.task('build', gulp.parallel(styles, scripts))

exports.default = build
