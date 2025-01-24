import gulp from 'gulp'
import autoprefixer from 'gulp-autoprefixer'
import esbuild from 'gulp-esbuild'
import { sassPlugin } from 'esbuild-sass-plugin'

const scripts = () => {
  return gulp.src(['src/Resources/public/cokiban.js'])
    .pipe(esbuild({
      bundle: true,
      drop: ['debugger', 'console'],
      minify: true,
      outfile: 'cokiban.min.js',
    }))
    .pipe(gulp.dest('src/Resources/public/'))
}

const styles = () => {
  return gulp.src(['src/Resources/public/cokiban.scss'])
    .pipe(esbuild({
      plugins: [ sassPlugin() ],
      minify: true,
      outfile: 'cokiban.min.css',
    }))
    .pipe(autoprefixer())
    .pipe(gulp.dest('src/Resources/public/'))
}

gulp.task('build', gulp.parallel(scripts, styles))
