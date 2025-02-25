<script setup>
import { ref, useTemplateRef, onMounted } from 'vue';
import LinksBreadcrumb from '@/components/primevue/LinksBreadcrumb.vue';
import MobileSidebarNavDrawer from '@/components/layout/admin/MobileSidebarNavDrawer.vue';
import TopNav from '@/components/layout/admin/TopNav.vue';
import Footer from '@/components/layout/admin/Footer.vue';
import SideMenuItems from '@/components/layout/admin/SideMenuItems.vue';

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

// Drawer menus
const navDrawerOpen = ref(false);

// Dynamic header height for arbitrary class styling
const headerHeight = ref('');
const siteHeader = useTemplateRef('site-header');
onMounted(() => {
    if (siteHeader.value) {
        headerHeight.value = `${siteHeader.value.offsetHeight}px`;
    }
});
</script>

<template>
    <div class="h-screen flex flex-col">
        <Teleport to="body">
            <Toast position="top-center" />
            <MobileSidebarNavDrawer v-model="navDrawerOpen">
                <SideMenuItems />
            </MobileSidebarNavDrawer>
        </Teleport>

        <header
            id="site-header"
            ref="site-header"
            class="block lg:fixed top-0 left-0 right-0 z-50"
        >
            <!-- Main Nav -->
            <TopNav @open-nav="navDrawerOpen = true" />
        </header>

        <div class="flex-1">
            <!-- Desktop Sidebar -->
            <aside :class="[
                'w-[18rem] inset-0 hidden lg:block fixed overflow-y-auto overflow-x-hidden dynamic-bg border-r dynamic-border',
                `top-[${headerHeight}]`,
            ]">
                <div class="w-full px-8 py-6">
                    <SideMenuItems />
                </div>
            </aside>

            <!-- Scrollable Content -->
            <div :class="[
                'flex flex-col h-full lg:pl-[18rem]',
                `lg:pt-[${headerHeight}]`,
            ]">
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
                    <slot />
                </main>

                <footer>
                    <Footer />
                </footer>
            </div>
        </div>
    </div>
</template>
