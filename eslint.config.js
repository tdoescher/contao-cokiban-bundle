import globals from 'globals';
import js from '@eslint/js';

export default [
    {
        ignores: [ './src/Resources/contao/public/cokiban.min.js' ],
        languageOptions: {
            'globals': globals.browser,
        },
        rules: {
            'comma-dangle': [ 'error', 'always-multiline' ],
            'arrow-parens': [ 'error' ],
            'indent': [ 'error', 4, { 'SwitchCase': 1 } ],
            'prefer-arrow-callback': [ 'error' ],
            'semi': [ 'error', 'always' ],
            'quotes': [ 'error', 'single' ],
        },
    },
    js.configs.recommended,
];
