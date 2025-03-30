import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';

import {
    defineConfigWithVueTs,
    vueTsConfigs,
} from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
    vue.configs['flat/recommended'],
    vueTsConfigs.recommended,
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
        ],
    },
    {
        rules: {
            'vue/require-default-prop': 'off',
            'vue/attribute-hyphenation': 'off',
            'vue/v-on-event-hyphenation': 'off',
            'vue/multi-word-component-names': 'off',
            'vue/block-lang': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
        },
    },
    prettier
);
