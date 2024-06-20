import globals from 'globals'
import pluginJs from '@eslint/js'

export default [
  {
    ignores: ['*.bundle.min.js'],
    languageOptions: {
      'globals': globals.browser,
    },
    rules: {
      'comma-dangle': ['error', 'always-multiline'],
      'arrow-parens': ['error'],
      'indent': ['error', 2, { 'SwitchCase': 1 }],
      'prefer-arrow-callback': ['error'],
      'semi': ['error', 'never'],
      'quotes': ['error', 'single'],
    },
  },
  pluginJs.configs.recommended,
]
