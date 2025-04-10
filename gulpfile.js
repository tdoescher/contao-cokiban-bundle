import gulp from 'gulp';
import esbuild from 'gulp-esbuild';
import { sassPlugin } from 'esbuild-sass-plugin';

const scripts = () => {
    return gulp.src([ 'src/Resources/public/cokiban.js' ])
        .pipe(esbuild({
            bundle: true,
            drop: [ 'debugger', 'console' ],
            minify: true,
            outfile: 'cokiban.min.js',
        }))
        .pipe(gulp.dest('src/Resources/public/'));
};

const styles = () => {
    return gulp.src([ 'src/Resources/public/cokiban.scss' ])
        .pipe(esbuild({
            target: [ 'es2020', 'chrome58', 'edge16', 'firefox57', 'node12', 'safari11' ],
            plugins: [ sassPlugin() ],
            minify: true,
            outfile: 'cokiban.min.css',
        }))
        .pipe(gulp.dest('src/Resources/public/'));
};

gulp.task('build', gulp.parallel(scripts, styles));
