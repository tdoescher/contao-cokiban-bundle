import esbuild from 'esbuild';
import { sassPlugin } from 'esbuild-sass-plugin';

const scripts = async () => {
    try {
        await esbuild.build({
            entryPoints: [ 'src/public/cokiban.js' ],
            bundle: true,
            drop: [ 'debugger', 'console' ],
            minify: true,
            outfile: 'src/public/cokiban.min.js',
        });
        // eslint-disable-next-line no-unused-vars
    } catch (e) {
        /* empty */
    }
};

const styles = async () => {
    try {
        await esbuild.build({
            entryPoints: [ `src/public/cokiban.scss` ],
            target: [ 'es2020', 'chrome58', 'edge16', 'firefox57', 'node12', 'safari11' ],
            plugins: [ sassPlugin() ],
            minify: true,
            outfile: 'src/public/cokiban.min.css',
        });
        // eslint-disable-next-line no-unused-vars
    } catch (e) {
        /* empty */
    }
};

await Promise.all([ scripts(), styles() ]);
