import esbuild from 'esbuild';
import { sassPlugin } from 'esbuild-sass-plugin';

const scripts = async () => {
    try {
        await esbuild.build({
            entryPoints: [ 'public/cokiban.js' ],
            bundle: true,
            drop: [ 'debugger', 'console' ],
            minify: true,
            outfile: 'public/cokiban.min.js',
        });
        // eslint-disable-next-line no-unused-vars
    } catch (e) {
        /* empty */
    }
};

const styles = async () => {
    try {
        await esbuild.build({
            entryPoints: [ 'public/cokiban.scss' ],
            target: [ 'es2022', 'chrome90', 'edge90', 'firefox90', 'safari14' ],
            plugins: [ sassPlugin() ],
            minify: true,
            outfile: 'public/cokiban.min.css',
        });
        // eslint-disable-next-line no-unused-vars
    } catch (e) {
        /* empty */
    }
};

await Promise.all([ scripts(), styles() ]);
