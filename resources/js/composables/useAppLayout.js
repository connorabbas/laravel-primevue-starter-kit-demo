import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { LayoutGrid, House, Info, Github, Code, Settings, LogOut, Users, Lock } from 'lucide-vue-next';

export function useAppLayout() {
    const page = usePage();
    const currentRoute = computed(() => {
        // Access page.url to trigger re-computation on navigation.
        /* eslint-disable @typescript-eslint/no-unused-vars */
        const url = page.url;
        /* eslint-enable @typescript-eslint/no-unused-vars */
        return route().current();
    });

    // Menu items
    const menuItems = computed(() => {
        let items = [
            {
                label: 'Home',
                lucideIcon: House,
                route: route('welcome'),
                active: currentRoute.value == 'welcome',
            },
            {
                label: 'Dashboard',
                lucideIcon: LayoutGrid,
                route: route('dashboard'),
                active: currentRoute.value == 'dashboard',
            },
            {
                label: 'Info',
                lucideIcon: Info,
                items: [
                    {
                        label: 'PrimeVue Docs',
                        url: 'https://primevue.org/',
                        lucideIcon: Code,
                    },
                    {
                        label: 'Starter Kit Repo',
                        url: 'https://github.com/connorabbas/laravel-primevue-starter-kit',
                        lucideIcon: Github,
                    },
                ],
            },
        ];

        if (page.props.auth.isAdmin) {
            items.push({
                label: 'Admin',
                lucideIcon: Lock,
                items: [
                    {
                        label: 'Dashboard',
                        lucideIcon: LayoutGrid,
                        route: route('admin.dashboard'),
                        active: currentRoute.value == 'admin.dashboard',
                    },
                    {
                        label: 'Users',
                        lucideIcon: Users,
                        route: route('admin.users.index'),
                        active: currentRoute.value == 'admin.users.index',
                    },
                ],
            });
        }

        return items;
    });

    // User menu and logout functionality.
    const logoutForm = useForm({});
    const logout = () => {
        logoutForm.post(route('logout'));
    };
    const userMenuItems = [
        {
            label: 'Settings',
            route: route('profile.edit'),
            lucideIcon: Settings,
        },
        {
            separator: true
        },
        {
            label: 'Log Out',
            lucideIcon: LogOut,
            command: () => logout(),
        },
    ];

    // Mobile menu
    const mobileMenuOpen = ref(false);
    const windowWidth = ref(window.innerWidth);
    const updateWidth = () => {
        windowWidth.value = window.innerWidth;
    };
    onMounted(() => {
        window.addEventListener('resize', updateWidth);
    });
    onUnmounted(() => {
        window.removeEventListener('resize', updateWidth);
    });
    watchEffect(() => {
        if (windowWidth.value > 1024) {
            mobileMenuOpen.value = false;
        }
    });

    return {
        currentRoute,
        menuItems,
        userMenuItems,
        mobileMenuOpen,
        logout,
    };
}
