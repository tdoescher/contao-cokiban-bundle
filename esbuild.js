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
        console.log('\x1b[32mSUCCESS\x1b[0m', 'Scripts bundled');
    } catch (e) {
        console.log('\x1b[31mERROR\x1b[0m', `Scripts: ${ e.message }`);
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
        console.log('\x1b[32mSUCCESS\x1b[0m', 'Styles bundled');
    } catch (e) {
        console.log('\x1b[31mERROR\x1b[0m', `Styles: ${ e.message }`);
    }
};

await Promise.all([ scripts(), styles() ]);
