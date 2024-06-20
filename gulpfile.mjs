import autoprefixer from 'autoprefixer'
import cssnano from 'cssnano'
import esbuild from 'gulp-esbuild'
import gulp from 'gulp'
import postcss from 'gulp-postcss'
import rename from 'gulp-rename'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'

const sass = gulpSass(dartSass)

const styles = () => {
  return gulp.src('src/Resources/public/cokiban.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([
      autoprefixer(),
      cssnano({ preset: ['default', { discardComments: { removeAll: true } }] }),
    ]))
    .pipe(rename({ extname: '.min.css' }))
    .pipe(gulp.dest('src/Resources/public/'))
}

const scripts = () => {
  return gulp.src('src/Resources/public/cokiban.js')
    .pipe(esbuild({
      outfile: 'cokiban.min.js',
      minify: true,
    }))
    .pipe(gulp.dest('src/Resources/public/'))
}

gulp.task('build', gulp.parallel(styles, scripts))
