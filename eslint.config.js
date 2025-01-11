// https://eslint.vuejs.org/user-guide/
// https://www.npmjs.com/package/@vue/eslint-config-typescript
// https://typescript-eslint.io/rules/?=recommended

import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import vueTsEslintConfig from '@vue/eslint-config-typescript';

export default [
    js.configs.recommended,
    ...pluginVue.configs['flat/essential'],
    ...vueTsEslintConfig(),
    {
        languageOptions: {
            ecmaVersion: 'latest',
            globals: {
                Ziggy: 'readonly',
            },
        },
        rules: {
            'vue/multi-word-component-names': 'off',
            'vue/block-lang': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
        },
    },
];
