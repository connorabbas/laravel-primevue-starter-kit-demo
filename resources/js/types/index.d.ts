import { DataTableFilterMetaData } from 'primevue';
import { MenuItem } from 'primevue/menuitem';
import type { LucideIcon } from 'lucide-vue-next';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface AuthProps {
    isAdmin?: boolean;
    user?: User;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: AuthProps;
};

export type PrimeVueDataFilters = {
    [key: string]: DataTableFilterMetaData;
};

export interface ExtendedMenuItem extends MenuItem {
    route?: string;
    lucideIcon?: LucideIcon;
}
