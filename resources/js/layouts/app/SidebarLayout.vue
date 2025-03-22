<script setup>
import { ref, computed, useTemplateRef, onMounted, onUnmounted, watchEffect } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import LinksMenu from '@/components/primevue/LinksMenu.vue';
import LinksPanelMenu from '@/components/primevue/LinksPanelMenu.vue';
import LinksBreadcrumb from '@/components/primevue/LinksBreadcrumb.vue';

defineProps({
    pageTitle: {
        type: String,
        required: false,
    },
    breadcrumbs: {
        type: Array,
        required: false,
        default: () => [],
    },
});

/* eslint-disable @typescript-eslint/no-unused-vars */
const tailwindArbitraryValues = [
    'w-[18rem]', // sidebar width
    'lg:pl-[18rem]',
    'top-[57px]', // 57px header height for aura & nora theme
    'lg:pt-[57px]',
    'top-[61px]', // 61px header height for lara theme
    'lg:pt-[61px]',
    'top-[64px]', // 64px header height for material theme
    'lg:pt-[64px]',
];
/* eslint-enable @typescript-eslint/no-unused-vars */

const page = usePage();
const currentRoute = computed(() => {
    // Access page.url to trigger re-computation on navigation.
    /* eslint-disable @typescript-eslint/no-unused-vars */
    const url = page.url;
    /* eslint-enable @typescript-eslint/no-unused-vars */
    return route().current();
});

const menuItems = computed(() => [
    {
        label: 'Home',
        icon: 'pi pi-home',
        route: route('welcome'),
        active: currentRoute.value == 'welcome',
    },
    {
        label: 'Dashboard',
        icon: 'pi pi-th-large',
        route: route('dashboard'),
        active: currentRoute.value == 'dashboard',
    },
    {
        label: 'Info',
        icon: 'pi pi-info-circle',
        items: [
            {
                label: 'PrimeVue Docs',
                url: 'https://primevue.org/',
                icon: 'pi pi-prime'
            },
            {
                label: 'Starter Kit Repo',
                url: 'https://github.com/connorabbas/laravel-primevue-starter-kit',
                icon: 'pi pi-github'
            }
        ],
    },
]);

// User menu
const logoutForm = useForm({});
const logout = () => {
    logoutForm.post(route('logout'));
};
const userMenuItems = [
    {
        label: 'Settings',
        route: route('profile.edit'),
        icon: 'pi pi-fw pi-cog',
    },
    {
        label: 'Log Out',
        icon: 'pi pi-fw pi-sign-out',
        command: () => logout(),
    },
];
const userMenu = useTemplateRef('user-menu');
const toggleUserMenu = (event) => {
    userMenu.value.childRef.toggle(event);
};
const mobileUserMenu = useTemplateRef('mobile-user-menu');
const toggleMobileUserMenu = (event) => {
    mobileUserMenu.value.childRef.toggle(event);
};

// Mobile drawer menu
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
</script>

<template>
    <div class="h-screen flex flex-col">
        <Teleport to="body">
            <!-- Mobile drawer menu -->
            <Drawer
                v-model:visible="mobileMenuOpen"
                position="right"
            >
                <div>
                    <LinksPanelMenu
                        :model="menuItems"
                        class="w-full"
                    />
                </div>
                <template #footer>
                    <div class="flex flex-col">
                        <Button
                            id="mobile-user-menu-btn"
                            severity="secondary"
                            :label="page.props.auth.user.name"
                            size="large"
                            icon="pi pi-sort"
                            iconPos="right"
                            pt:root:class="flex justify-between"
                            @click="toggleMobileUserMenu($event)"
                        />
                        <LinksMenu
                            ref="mobile-user-menu"
                            :model="userMenuItems"
                            popup
                        />
                    </div>
                </template>
            </Drawer>
        </Teleport>

        <!-- Mobile Header -->
        <header
            id="site-header"
            ref="site-header"
            class="block lg:fixed top-0 left-0 right-0 z-50"
        >
            <nav class="dynamic-bg shadow-sm flex justify-between items-center lg:hidden">
                <Container class="grow">
                    <div class="flex justify-between items-center py-4">
                        <div>
                            <InertiaLink :href="route('welcome')">
                                <ApplicationLogo
                                    class="block h-8 w-auto fill-current text-surface-900 dark:text-surface-0"
                                />
                            </InertiaLink>
                        </div>
                        <div>
                            <Button
                                severity="secondary"
                                icon="pi pi-bars"
                                pt:icon:class="text-xl"
                                text
                                @click="mobileMenuOpen = true"
                            />
                        </div>
                    </div>
                </Container>
            </nav>
        </header>

        <div class="flex-1">
            <!-- Desktop Sidebar -->
            <aside
                class="w-[18rem] inset-0 hidden lg:block fixed overflow-y-auto overflow-x-hidden dynamic-bg border-r dynamic-border"
            >
                <div class="w-full h-full flex flex-col justify-between p-4">
                    <div class="space-y-6">
                        <div class="p-2">
                            <InertiaLink :href="route('welcome')">
                                <ApplicationLogo
                                    class="block h-10 w-auto fill-current text-surface-900 dark:text-surface-0"
                                />
                            </InertiaLink>
                        </div>
                        <div>
                            <LinksPanelMenu
                                :model="menuItems"
                                class="w-full"
                            />
                        </div>
                    </div>
                    <div>
                        <Button
                            id="user-menu-btn"
                            pt:root:class="flex justify-between"
                            :label="page.props.auth.user.name"
                            severity="secondary"
                            size="large"
                            icon="pi pi-sort"
                            iconPos="right"
                            fluid
                            @click="toggleUserMenu($event)"
                        />
                        <LinksMenu
                            ref="user-menu"
                            :model="userMenuItems"
                            popup
                        />
                    </div>
                </div>
            </aside>

            <!-- Scrollable Content -->
            <div class="flex flex-col h-full lg:pl-[18rem]">
                <!-- Breadcrumbs Nav -->
                <nav
                    v-if="breadcrumbs.length"
                    class="dynamic-bg border-b dynamic-border"
                >
                    <Container fluid>
                        <LinksBreadcrumb
                            :model="breadcrumbs"
                            class="py-4"
                        />
                    </Container>
                </nav>

                <!-- Page Content -->
                <main
                    id="page-content"
                    class="grow"
                >
                    <Container
                        vertical
                        fluid
                    >
                        <slot />
                    </Container>
                </main>

                <footer>
                    <!-- Add footer content as desired -->
                </footer>
            </div>
        </div>
    </div>
</template>
