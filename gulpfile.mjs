import fs from 'node:fs'
import path from 'node:path'
import url from 'node:url'
import gulp from 'gulp'
import esbuild from 'gulp-esbuild'
import { sassPlugin } from 'esbuild-sass-plugin'

const style = () => {
  return gulp.src(['src/Resources/public/cokiban.scss'])
  .pipe(esbuild({
    plugins: [ sassPlugin() ],
    minify: true,
    outfile: 'cokiban.min.css',
  }))
  .pipe(gulp.dest('src/Resources/public/'))
}

const script = () => {
  return gulp.src(['src/Resources/public/cokiban.js'])
  .pipe(esbuild({
    bundle: true,
    drop: ['debugger', 'console'],
    minify: true,
    outfile: 'cokiban.min.js',
  }))
  .pipe(gulp.dest('src/Resources/public/'))
}

gulp.task('build', gulp.parallel(style, script))
