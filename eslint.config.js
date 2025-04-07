import globals from 'globals';
import js from '@eslint/js';

export default [
    {
        ignores: [ './app/*' ],
        languageOptions: {
            'globals': globals.browser,
        },
        rules: {
            'comma-dangle': [ 'error', 'always-multiline' ],
            'arrow-parens': [ 'error' ],
            'indent': [ 'error', 4, { 'SwitchCase': 2 } ],
            'prefer-arrow-callback': [ 'error' ],
            'semi': [ 'error', 'always' ],
            'quotes': [ 'error', 'single' ],
        },
    },
    js.configs.recommended,
];
