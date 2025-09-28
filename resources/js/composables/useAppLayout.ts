import { ref, computed, onMounted, onUnmounted, watchEffect } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import {
    LayoutGrid, House, Info, Settings, LogOut, ExternalLink, FileSearch, FolderGit2, Lock, Users, Lightbulb, Table2, BookOpen,
    Logs
} from 'lucide-vue-next'
import { MenuItem } from '@/types'

export function useAppLayout() {
    const page = usePage()
    const currentRoute = computed(() => {
        // Access page.url to trigger re-computation on navigation.
        /* eslint-disable @typescript-eslint/no-unused-vars */
        const url = page.url
        /* eslint-enable @typescript-eslint/no-unused-vars */
        return route().current()
    })

    // Menu items
    const menuItems = computed<MenuItem[]>(() => [
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
            label: 'Resources',
            lucideIcon: Info,
            items: [
                {
                    label: 'Laravel Docs',
                    url: 'https://laravel.com/docs/master',
                    target: '_blank',
                    lucideIcon: ExternalLink,
                },
                {
                    label: 'PrimeVue Docs',
                    url: 'https://primevue.org/',
                    target: '_blank',
                    lucideIcon: ExternalLink,
                },
                {
                    label: 'Starter Kit Docs',
                    url: 'https://connorabbas.github.io/laravel-primevue-starter-kit-docs/',
                    target: '_blank',
                    lucideIcon: FileSearch,
                },
                {
                    label: 'Starter Kit Repo',
                    url: 'https://github.com/connorabbas/laravel-primevue-starter-kit',
                    target: '_blank',
                    lucideIcon: FolderGit2,
                },
            ],
        },
        {
            label: 'Examples',
            lucideIcon: Lightbulb,
            items: [
                {
                    label: 'Sidebar + DataTable',
                    lucideIcon: Table2,
                    route: route('examples.data-table.contacts.index'),
                    active: currentRoute.value == 'examples.data-table.contacts.index',
                },
                {
                    label: 'Paginator + Sort & Filter',
                    lucideIcon: BookOpen,
                    route: route('examples.paginator.contacts.index'),
                    active: currentRoute.value == 'examples.paginator.contacts.index',
                },
            ],
        },
        {
            label: 'Admin',
            lucideIcon: Lock,
            visible: page.props.auth.isAdmin,
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
                {
                    label: 'Logs',
                    lucideIcon: Logs,
                    url: '/log-viewer',
                },
            ],
        },
    ])

    // User menu and logout functionality.
    const logoutForm = useForm({})
    const logout = () => {
        logoutForm.post(route('logout'))
    }
    const userMenuItems: MenuItem[] = [
        {
            label: 'Settings',
            route: route('profile.edit'),
            lucideIcon: Settings,
        },
        {
            separator: true
        },
        {
            label: 'Log out',
            lucideIcon: LogOut,
            command: () => logout(),
        },
    ]

    // Mobile menu
    const mobileMenuOpen = ref(false)
    if (typeof window !== 'undefined') {
        const windowWidth = ref(window.innerWidth)
        const updateWidth = () => {
            windowWidth.value = window.innerWidth
        }
        onMounted(() => {
            window.addEventListener('resize', updateWidth)
        })
        onUnmounted(() => {
            window.removeEventListener('resize', updateWidth)
        })
        watchEffect(() => {
            if (windowWidth.value > 1024) {
                mobileMenuOpen.value = false
            }
        })
    }

    return {
        currentRoute,
        menuItems,
        userMenuItems,
        mobileMenuOpen,
        logout,
    }
}
