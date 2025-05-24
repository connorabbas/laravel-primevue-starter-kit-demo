import { useColorMode, type BasicColorSchema, type UseColorModeOptions } from '@vueuse/core';
import { useCookies } from '@vueuse/integrations/useCookies';
import type { CookieSetOptions } from 'universal-cookie';
import { watch } from 'vue';

interface SiteColorModeOptions extends UseColorModeOptions {
    cookieKey?: string;
    cookieOpts?: CookieSetOptions;
    initialOnServer?: BasicColorSchema;
}

export function useSiteColorMode(opts: SiteColorModeOptions = {}) {
    const cookies = useCookies(['colorScheme']);
    const initialOnServer = opts.initialOnServer;
    const initialValue = typeof window === 'undefined'
        ? (initialOnServer ?? 'auto')
        : (cookies.get('colorScheme') as BasicColorSchema) ?? 'auto';

    const colorMode = useColorMode({ initialValue, ...opts });

    if (typeof window !== 'undefined') {
        watch(colorMode, val => {
            cookies.set('colorScheme', val, opts.cookieOpts);
        });
    }

    return colorMode;
}
